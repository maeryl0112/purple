<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    //daily sales report
    public function dailyReport(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());
        $user = auth()->user();
    
        // Determine the branch filter based on the user's role
        if ($user->role_id == 2) { // Assuming role_id 2 is for employees
            $selectedBranch = $user->branch_id; // Employees are restricted to their assigned branch
        } else {
            $selectedBranch = $request->input('branch_id'); // Admins can select any branch
        }
    
        // Query data for the selected date and branch
        $reports = Appointment::selectRaw('
                appointments.date,
                SUM(appointments.total) as total_sales,
                COUNT(appointments.id) as appointment_count,
                GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id') // Join branches through employees
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users/customers table
            ->where('appointments.status', 1) // Assuming 1 means completed
            ->whereDate('appointments.date', $selectedDate) // Filter by the selected date
            ->when($selectedBranch, function ($query, $selectedBranch) {
                return $query->where('employees.branch_id', $selectedBranch); // Filter by branch
            })
            ->groupBy('appointments.date')
            ->get();
    
        // Process services_with_details into structured data
        $reports->transform(function ($report) {
            $services = explode(',', $report->services_with_details);
    
            $groupedServices = collect($services)->map(function ($service) {
                [$name, $price, $employee, $customer] = explode(':', $service);
                return [
                    'name' => $name,
                    'price' => (float) $price,
                    'employee' => $employee,
                    'customer' => $customer,
                ];
            })->groupBy('name')->map(function ($group) {
                return [
                    'total_price' => $group->sum('price'),
                    'details' => $group,
                ];
            });
    
            $report->grouped_services = $groupedServices;
            return $report;
        });
    
        $grandTotal = $reports->sum('total_sales');
    
        // Fetch branches for dropdown
        if ($user->role_id == 2) {
            $branches = \App\Models\Branch::where('id', $user->branch_id)->get(); // Employees see only their branch
        } else {
            $branches = \App\Models\Branch::all(); // Admins see all branches
        }
    
        return view('reports.daily', compact('reports', 'grandTotal', 'selectedDate', 'selectedBranch', 'branches'));
    }
    


    
    public function downloadPDF(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());
        $user = auth()->user();
    
        // Restrict branch filtering based on user role
        if ($user->role_id == 2) { // Assuming role_id 2 is for employees
            $selectedBranch = $user->branch_id; // Employees can only see their assigned branch
        } else {
            $selectedBranch = $request->input('branch_id'); // Admins can choose any branch
        }
    
        $reports = Appointment::selectRaw('
        appointments.date,
        branches.name as branch_name, 
        SUM(appointments.total) as total_sales,
        COUNT(appointments.id) as appointment_count,
        GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')  // Include branch name
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->where('appointments.status', 1)
            ->whereDate('appointments.date', $selectedDate)
            ->when($selectedBranch, function ($query, $selectedBranch) {
                return $query->where('appointments.branch_id', $selectedBranch);
            })
            ->groupBy('appointments.date', 'branches.name') // Group by branch name
            ->get();
    
        // Process services_with_details into structured data
        $reports->transform(function ($report) {
            $services = explode(',', $report->services_with_details);
            $groupedServices = collect($services)->map(function ($service) {
                [$name, $price, $employee, $customer] = explode(':', $service);
                return [
                    'name' => $name,
                    'price' => (float) $price,
                    'employee' => $employee,
                    'customer' => $customer,
                ];
            })->groupBy('name')->map(function ($group) {
                return [
                    'total_price' => $group->sum('price'),
                    'details' => $group,
                ];
            });
    
            $report->grouped_services = $groupedServices;
            return $report;
        });
    
        $image = public_path('images/banner-purple.png');
        $grandTotal = $reports->sum('total_sales');
        $preparedBy = $user->name ?? 'System Admin';
        $currentDateTime = now()->format('Y-m-d H:i:s');
        $branchAssigned = $user->branch?->name ?? 'All Branches';
        // Generate the PDF
        $pdf = Pdf::loadView('reports.daily-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'grandTotal', 'image', 'selectedDate', 'selectedBranch','branchAssigned'));
    
        return $pdf->download('daily-sales-report-' . $selectedDate . '.pdf');
    }
    
    
    public function downloadAllPDF()
{
    // Get the logged-in employee's branch_id and branch name
    $branchId = auth()->user()->branch_id;
    $selectedBranch = auth()->user()->branch->name ?? 'N/A';

    $reports = Appointment::selectRaw('
    appointments.date,
    branches.name as branch_name,
    SUM(appointments.total) as total_sales,
    COUNT(appointments.id) as appointment_count,
    GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
    ')
    ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
    ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
    ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
    ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')  // Include branch name
    ->where('appointments.status', 1) // Only completed appointments
    ->when(auth()->user()->branch_id, function ($query) {
        $query->where('appointments.branch_id', auth()->user()->branch_id); // Filter by employee's branch
    })
    ->groupBy('appointments.date', 'branches.name') // Group by date and branch name
    ->orderBy('appointments.date', 'desc')
    ->get();


    // Process services_with_details into structured data
    $reports->transform(function ($report) {
        if (!empty($report->services_with_details)) {
            $services = explode(',', $report->services_with_details);
            $groupedServices = collect($services)->map(function ($service) {
                [$name, $price, $employee, $customer] = explode(':', $service);
                return [
                    'name' => $name,
                    'price' => (float) $price,
                    'employee' => $employee,
                    'customer' => $customer,
                ];
            })->groupBy('name')->map(function ($group) {
                return [
                    'total_price' => $group->sum('price'),
                    'details' => $group,
                ];
            });

            $report->grouped_services = $groupedServices;
        } else {
            $report->grouped_services = collect(); // Empty collection for consistency
        }
        return $report;
    });

    $image = public_path('images/banner-purple.png');
    $grandTotal = $reports->sum('total_sales');
    $preparedBy = auth()->user()->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');
    $selectedDate = now(); // Example: Add the selected date dynamically
    $branchAssigned = auth()->user()->branch->name ?? 'All Branches';


    // Generate the PDF
    $pdf = Pdf::loadView('reports.all-daily-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'grandTotal', 'image', 'selectedBranch', 'selectedDate','branchAssigned'));

    return $pdf->download('all-daily-sales-reports.pdf');
}





    //weekly
    public function weeklyReport(Request $request)
    {
        // Get the selected week (format: YYYY-WW), default to the current week
        $selectedWeek = $request->input('week', Carbon::now()->format('Y-\WW'));
        $user = auth()->user();
    
        // Determine the branch filter based on the user's role
        if ($user->role_id == 2) { // Assuming role_id 2 is for employees
            $selectedBranch = $user->branch_id; // Employees are restricted to their assigned branch
        } else {
            $selectedBranch = $request->input('branch_id'); // Admins can select any branch
        }
    
        // Extract year and week number
        [$year, $week] = explode('-W', $selectedWeek);
    
        // Calculate start and end of the week
        $startOfWeek = Carbon::createFromFormat('Y-m-d', "$year-01-01")
            ->startOfYear()
            ->addWeeks($week - 1)
            ->startOfWeek();
    
        $endOfWeek = $startOfWeek->copy()->endOfWeek();
    
        // Query for the weekly data
        $reports = Appointment::selectRaw('
                date,
                SUM(total) as total_sales,
                GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name) SEPARATOR "|") as services_with_details
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id') // Join branches through employees
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->when($selectedBranch, function ($query, $selectedBranch) {
                return $query->where('employees.branch_id', $selectedBranch); // Filter by branch
            })
            ->where('appointments.status', 1) // Completed appointments
            ->whereBetween('appointments.date', [$startOfWeek, $endOfWeek])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    
        // Process services_with_details into structured data
        $reports->transform(function ($report) {
            $services = explode('|', $report->services_with_details);
    
            $groupedServices = collect($services)->map(function ($service) {
                [$name, $price, $employee, $customer] = explode(':', $service);
                return [
                    'name' => $name,
                    'price' => (float) $price,
                    'employee' => $employee,
                    'customer' => $customer,
                ];
            })->groupBy('name')->map(function ($group) {
                return [
                    'total_price' => $group->sum('price'),
                    'details' => $group,
                ];
            });
    
            $report->grouped_services = $groupedServices;
            return $report;
        });
    
        $grandTotal = $reports->sum('total_sales');


        if ($user->role_id == 2) {
            $branches = \App\Models\Branch::where('id', $user->branch_id)->get(); // Employees see only their branch
        } else {
            $branches = \App\Models\Branch::all(); // Admins see all branches
        }
    
      
        return view('reports.weekly', compact('reports', 'grandTotal', 'selectedWeek', 'branches', 'selectedBranch'));
    }
    

    //specific week
    public function downloadAllWeeklyPDF()
    {
        $reports = Appointment::selectRaw('
        YEARWEEK(`date`, 1) as week,
        MIN(`date`) as week_start,
        MAX(`date`) as week_end,
        SUM(`total`) as total_sales,
        COUNT(appointments.id) as appointment_count,
        GROUP_CONCAT(DISTINCT services.name) as services,
        GROUP_CONCAT(DISTINCT services.price) as prices,
        GROUP_CONCAT(DISTINCT employees.first_name) as employees,
        GROUP_CONCAT(DISTINCT users.name) as customers
    ')
    ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
    ->leftJoin('services', 'appointments.service_id', '=', 'services.id') // Join with services
    ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join with customers (users)
    ->where('appointments.status', 2) // Assuming 2 means completed
    ->groupBy('week')
    ->orderBy('week', 'DESC')
    ->get();

        $image = public_path('images/banner-purple.png');
        $preparedBy = auth()->user()->name ?? 'System Admin';
        $currentDateTime = now()->format('Y-m-d H:i:s');

        $pdf = Pdf::loadView('reports.all-weekly-pdf', compact('reports', 'preparedBy', 'currentDateTime','image'));
        return $pdf->download('weekly-sales-report.pdf');
    }

    public function downloadWeeklyPDF()
{
    // Get the current year and week number
    $currentYearWeek = now()->format('oW'); // 'o' for ISO year, 'W' for ISO week

    // Query to fetch weekly data with services, employees, prices, and customers
    $reports = Appointment::selectRaw('
                YEARWEEK(`date`, 1) as week,
                MIN(`date`) as week_start,
                MAX(`date`) as week_end,
                SUM(`total`) as total_sales,
                COUNT(appointments.id) as appointment_count,
                GROUP_CONCAT(DISTINCT services.name) as services,
                GROUP_CONCAT(DISTINCT services.price) as prices,
                GROUP_CONCAT(DISTINCT employees.first_name) as employees,
                GROUP_CONCAT(DISTINCT users.name) as customers
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join with users/customers
            ->where('appointments.status', 2) // Assuming 2 means completed
            ->having('week', '=', $currentYearWeek) // Filter for the current week
            ->groupBy('week')
            ->orderBy('week', 'DESC')
            ->get();

    // Path for the banner image
    $image = public_path('images/banner-purple.png');

    // Prepare report metadata
    $preparedBy = auth()->user()->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    // Load the PDF view with the weekly data
    $pdf = Pdf::loadView('reports.weekly-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'image'));

    // Return the PDF as a download with the filename formatted as year-week
    return $pdf->download('weekly-sales-report-' . now()->format('o-W') . '.pdf'); // Filename includes ISO year-week
}


public function monthlyReport(Request $request)
{
    // Get the selected month and branch ID
    $selectedMonth = $request->input('date', now()->format('Y-m'));
    $user = auth()->user();
    
        // Determine the branch filter based on the user's role
        if ($user->role_id == 2) { // Assuming role_id 2 is for employees
            $selectedBranch = $user->branch_id; // Employees are restricted to their assigned branch
        } else {
            $selectedBranch = $request->input('branch_id'); // Admins can select any branch
        }

    // Extract the year and month
    $year = \Carbon\Carbon::parse($selectedMonth)->year;
    $month = \Carbon\Carbon::parse($selectedMonth)->month;

    // Query data for the selected month
    $reports = Appointment::selectRaw('
            MONTH(appointments.date) as month,
            SUM(appointments.total) as total_sales,
            COUNT(appointments.id) as appointment_count,
            GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id') // Join branches through employees
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users/customers table
        ->where('appointments.status', 1) // Assuming 1 means completed
        ->whereYear('appointments.date', $year) // Filter by year
        ->whereMonth('appointments.date', $month) // Filter by month
        ->when($selectedBranch, function ($query, $selectedBranch) {
            return $query->where('employees.branch_id', $selectedBranch); // Filter by branch
        })
        ->groupByRaw('MONTH(appointments.date)')
        ->get();

    // Process services_with_details into structured data
    $reports->transform(function ($report) {
        $services = explode(',', $report->services_with_details);

        $groupedServices = collect($services)->map(function ($service) {
            [$name, $price, $employee, $customer] = explode(':', $service);
            return [
                'name' => $name,
                'price' => (float) $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        })->groupBy('name')->map(function ($group) {
            return [
                'total_price' => $group->sum('price'),
                'details' => $group,
            ];
        });

        $report->grouped_services = $groupedServices; // Attach grouped data to the report
        return $report;
    });

    $grandTotal = $reports->sum('total_sales'); // Calculate grand total
    
    if ($user->role_id == 2) {
        $branches = \App\Models\Branch::where('id', $user->branch_id)->get(); // Employees see only their branch
    } else {
        $branches = \App\Models\Branch::all(); // Admins see all branches
    }

    return view('reports.monthly', compact('reports', 'grandTotal', 'selectedMonth', 'selectedBranch', 'branches'));
}



    public function downloadMonthlyPdf()
    {
        $currentYear = now()->year; // Get the current year
        $currentMonth = now()->month; // Get the current month

        // Query to fetch monthly data with services, prices, employees, and customers
        $reports = Appointment::selectRaw('
                    YEAR(`date`) as year,
                    MONTH(`date`) as month,
                    SUM(`total`) as total_sales,
                    COUNT(appointments.id) as appointment_count,
                    GROUP_CONCAT(DISTINCT services.name) as services,
                    GROUP_CONCAT(DISTINCT services.price) as prices,
                    GROUP_CONCAT(DISTINCT employees.first_name) as employees,
                    GROUP_CONCAT(DISTINCT users.name) as customers
                ')
                ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
                ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
                ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join with users/customers
                ->where('appointments.status', 2) // Assuming 2 means completed
                ->whereYear('appointments.date', $currentYear) // Filter by the current year
                ->whereMonth('appointments.date', $currentMonth) // Filter by the current month
                ->groupBy('year', 'month')
                ->orderBy('year', 'DESC')
                ->orderBy('month', 'DESC')
                ->get();

        // Convert numeric month to month name in words using Carbon
        foreach ($reports as $report) {
            $report->month_name = Carbon::createFromDate($report->year, $report->month, 1)->format('F');
        }

        // Calculate the grand total for the month
        $grandTotal = $reports->sum('total_sales');

        // Prepare data for the PDF view
        $image = public_path('images/banner-purple.png');
        $preparedBy = auth()->user()->name ?? 'System Admin';
        $currentDateTime = now()->format('Y-m-d H:i:s');

        // Load the PDF view with the data
        $pdf = Pdf::loadView('reports.monthly-pdf', compact('reports', 'grandTotal', 'preparedBy', 'currentDateTime', 'image'));

        // Return the generated PDF for download
        return $pdf->download('monthly-sales-report-' . now()->format('F-Y') . '.pdf');
    }

    public function downloadAllMonthlyPDF()
    {
        $currentYear = now()->year; // Get the current year

        // Query to fetch monthly data with services, prices, employees, and customers
        $reports = Appointment::selectRaw('
                    YEAR(`date`) as year,
                    MONTH(`date`) as month,
                    SUM(`total`) as total_sales,
                    COUNT(appointments.id) as appointment_count,
                    GROUP_CONCAT(DISTINCT services.name) as services,
                    GROUP_CONCAT(DISTINCT services.price) as prices,
                    GROUP_CONCAT(DISTINCT employees.first_name) as employees,
                    GROUP_CONCAT(DISTINCT users.name) as customers
                ')
                ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
                ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
                ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join with users/customers
                ->where('appointments.status', 2) // Assuming 2 means completed
                ->whereYear('appointments.date', $currentYear) // Filter by the current year
                ->groupBy('year', 'month')
                ->orderBy('month', 'ASC') // Order by month to display from January to December
                ->get();

        // Convert numeric month to month name in words using Carbon
        foreach ($reports as $report) {
            $report->month_name = Carbon::createFromDate($report->year, $report->month, 1)->format('F');
        }

        $image = public_path('images/banner-purple.png');
        // Prepare the data for the PDF view
        $preparedBy = auth()->user()->name ?? 'System Admin'; // Use authenticated user or default to 'System Admin'
        $currentDateTime = now()->format('Y-m-d H:i:s'); // Current date and time

        // Generate the PDF with the data
        $pdf = Pdf::loadView('reports.all-monthly-pdf', compact('reports', 'preparedBy', 'currentDateTime','image'));

        // Download the PDF with a name based on the current month and year
        return $pdf->download('all-monthly-sales-report.pdf');
    }

    public function quarterlyReport(Request $request)
    {
        $selectedYear = $request->input('year', now()->year);
        $selectedQuarter = $request->input('quarter', ceil(now()->month / 3));
        $user = auth()->user();
    
        // Determine the branch filter based on the user's role
        $selectedBranch = $user->role_id == 2 ? $user->branch_id : $request->input('branch_id');
    
        $reports = Appointment::selectRaw('
                QUARTER(appointments.date) as quarter,
                YEAR(appointments.date) as year,
                SUM(appointments.total) as total_sales,
                COUNT(appointments.id) as appointment_count,
                GROUP_CONCAT(CONCAT_WS("|", services.name, services.price, employees.first_name, users.name)) as services_with_details
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
            ->where('appointments.status', 1)
            ->whereYear('appointments.date', $selectedYear)
            ->whereRaw('QUARTER(appointments.date) = ?', [$selectedQuarter])
            ->when($selectedBranch, fn($query) => $query->where('employees.branch_id', $selectedBranch))
            ->groupBy('quarter', 'year')
            ->get();
    
        // Process grouped services safely
        $reports->transform(function ($report) {
            $services = array_filter(explode(',', $report->services_with_details));
            $groupedServices = collect($services)->map(function ($service) {
                $parts = explode('|', $service);
                if (count($parts) !== 4) return null; // Avoid errors from incorrect format
                [$name, $price, $employee, $customer] = $parts;
                return [
                    'name' => $name,
                    'price' => (float) $price,
                    'employee' => $employee,
                    'customer' => $customer,
                ];
            })->filter()->groupBy('name')->map(fn($group) => [
                'total_price' => $group->sum('price'),
                'details' => $group,
            ]);
    
            $report->grouped_services = $groupedServices;
            return $report;
        });
    
        $grandTotal = $reports->sum('total_sales');
    
        $branches = ($user->role_id == 2)
        ? \App\Models\Branch::where('id', $user->branch_id)->get() // Employees see only their branch
        : \App\Models\Branch::all(); // Admins see all branches
    
        return view('reports.quarterly', compact('reports', 'grandTotal', 'selectedYear', 'selectedQuarter', 'selectedBranch', 'branches'));
    }
    



public function annualReport(Request $request)
{
    $selectedYear = $request->input('year', now()->year); // Default to the current year
    $user = auth()->user();
    
    // Determine the branch filter based on the user's role
    $selectedBranch = ($user->role_id == 2) 
        ? $user->branch_id // Employees are restricted to their assigned branch
        : $request->input('branch_id'); // Admins can select any branch

    $reports = Appointment::selectRaw('
            YEAR(appointments.date) as year,
            SUM(appointments.total) as total_sales,
            COUNT(appointments.id) as appointment_count,
            GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
        ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id') // Join branches through employees
        ->where('appointments.status', 1)
        ->whereYear('appointments.date', $selectedYear)
        ->when($selectedBranch, function ($query, $selectedBranch) {
            return $query->where('employees.branch_id', $selectedBranch); // Filter by branch
        })
        ->groupBy('year')
        ->get();

    // Transform the reports to group services with details
    $reports->transform(function ($report) {
        $services = explode(',', $report->services_with_details);
        $groupedServices = collect($services)->map(function ($service) {
            [$name, $price, $employee, $customer] = explode(':', $service);
            return [
                'name' => $name,
                'price' => (float) $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        })->groupBy('name')->map(function ($group) {
            return [
                'total_price' => $group->sum('price'),
                'details' => $group,
            ];
        });

        $report->grouped_services = $groupedServices;
        return $report;
    });

    $grandTotal = $reports->sum('total_sales');

    // Fetch branches for dropdown based on user role
    $branches = ($user->role_id == 2)
        ? \App\Models\Branch::where('id', $user->branch_id)->get() // Employees see only their branch
        : \App\Models\Branch::all(); // Admins see all branches

    return view('reports.annual', compact('reports', 'grandTotal', 'selectedYear', 'selectedBranch', 'branches'));
}


public function downloadQuarterlyPDF(Request $request)
{
    $currentYear = now()->year;
    $currentQuarter = now()->quarter;
    $user = auth()->user();

    // Determine the branch filter based on the user's role
    $selectedBranch = $user->role_id == 2 ? $user->branch_id : $request->input('branch_id');

    $reports = Appointment::selectRaw('
        QUARTER(appointments.date) as quarter,
        YEAR(appointments.date) as year,
        branches.name as branch_name,
        SUM(appointments.total) as total_sales,
        COUNT(appointments.id) as appointment_count,
        GROUP_CONCAT(CONCAT_WS("|", services.name, services.price, employees.first_name, users.name)) as services_with_details
    ')
    ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
    ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
    ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
    ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
    ->where('appointments.status', 1)
    ->whereYear('appointments.date', $currentYear)
    ->whereRaw('QUARTER(appointments.date) = ?', [$currentQuarter])
    ->when($selectedBranch, function ($query, $selectedBranch) {
        return $query->where('employees.branch_id', $selectedBranch);
    })
    ->groupBy('quarter', 'year', 'branches.name')
    ->get();

    // Transform the reports to group services correctly
    $reports->transform(function ($report) {
        if (!$report->services_with_details) {
            $report->grouped_services = []; // Ensure it's an empty array, not null
            return $report;
        }

        $services = explode(',', $report->services_with_details);
        $groupedServices = collect($services)->map(function ($service) {
            $parts = explode('|', $service);
            if (count($parts) < 4) return null; // Skip malformed data

            [$name, $price, $employee, $customer] = $parts;
            return [
                'name' => $name,
                'price' => (float) $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        })->filter()->groupBy('name')->map(function ($group) {
            return [
                'total_price' => $group->sum('price'),
                'details' => $group->toArray(),
            ];
        })->toArray();

        $report->grouped_services = $groupedServices; // Ensure it's an array
        return $report;
    });

    // Prepare additional data for the PDF
    $image = public_path('images/banner-purple.png');
    $preparedBy = $user->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    // Load the PDF view
    $pdf = Pdf::loadView('reports.quarterly-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'image'));

    // Download the PDF
    return $pdf->download("quarterly-sales-report-Q{$currentQuarter}-{$currentYear}.pdf");
}

    public function downloadAllQuarterlyPDF()
    {
        $user = auth()->user();

        // Determine the branch filter based on the user's role
        $selectedBranch = $user->role_id == 2 ? $user->branch_id : $request->input('branch_id');

            $reports = Appointment::selectRaw('
            QUARTER(appointments.date) as quarter,
            YEAR(appointments.date) as year,
            CONCAT("Q", QUARTER(appointments.date), " ", YEAR(appointments.date)) as quarter_label,
            MIN(appointments.date) as quarter_start,
            MAX(appointments.date) as quarter_end,
            SUM(appointments.total) as total_sales,
            COUNT(appointments.id) as appointment_count,
            COUNT(DISTINCT services.id) as service_count,
            GROUP_CONCAT(DISTINCT services.name) as services,
            GROUP_CONCAT(DISTINCT services.price) as prices,
            GROUP_CONCAT(DISTINCT employees.first_name) as employees,
            GROUP_CONCAT(DISTINCT users.name) as customers
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')   // Join with services
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id')  // Join with users/customers
        ->where('appointments.status', 2) // Assuming 2 means completed
        ->groupBy('year', 'quarter', 'quarter_label') // Correct grouping
        ->orderBy('year', 'DESC')
        ->orderBy('quarter', 'DESC')
        ->get();


        // Prepare additional data for the PDF view
        $image = public_path('images/banner-purple.png');
        $preparedBy = auth()->user()->name ?? 'System Admin';
        $currentDateTime = now()->format('Y-m-d H:i:s');

        // Load the PDF view with the data
        $pdf = Pdf::loadView('reports.all-quarterly-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'image'));

        // Download the PDF with a dynamic filename based on the current quarter and year
        return $pdf->download('quarterly-sales-report-Q.pdf');
    }

    public function downloadAnnualPdf()
    {
        $user = auth()->user();
        $branchId = $user->branch_id;
    
        // Load branch name
        $selectedBranch = $user->branch->name ?? 'N/A';
    
        // Get the current year
        $currentYear = now()->year;
    
        // Fetch reports for the current year
        $reportsQuery = Appointment::selectRaw('
                YEAR(appointments.date) as year,
                branches.name as branch_name,
                SUM(appointments.total) as total_sales,
                GROUP_CONCAT(CONCAT(services.name, ":", services.price)) as services_with_details
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id') // Include branch details
            ->where('appointments.status', 1) // Only completed appointments
            ->whereYear('appointments.date', $currentYear) // Filter for the current year
            ->groupByRaw('YEAR(appointments.date), branches.name');
    
        // Filter based on role
        if ($user->role_id == 2) { // Employee
            $reportsQuery->where('employees.branch_id', $branchId);
        }
    
        $reports = $reportsQuery->orderBy('year', 'desc')->get();
    
        // Transform the reports
        $reports->transform(function ($report) {
            $services = explode(',', $report->services_with_details);
            $groupedServices = collect($services)->map(function ($service) {
                [$name, $price] = explode(':', $service);
                return [
                    'name' => $name,
                    'price' => (float) $price,
                ];
            })->groupBy('name')->map(function ($group) {
                return [
                    'total_price' => $group->sum('price'),
                    'details' => $group,
                ];
            });
    
            $report->grouped_services = $groupedServices;
            return $report;
        });
    
        // Grand total
        $grandTotal = $reports->sum('total_sales');
    
        // Prepare additional data
        $image = public_path('images/banner-purple.png');
        $preparedBy = $user->name ?? 'System Admin';
        $currentDateTime = now()->format('Y-m-d H:i:s');
    
        // Pass all required data to the PDF view
        $pdf = Pdf::loadView('reports.annual-pdf', compact(
            'reports',
            'grandTotal',
            'preparedBy',
            'currentDateTime',
            'image',
            'selectedBranch'
        ));
    
        // Download the PDF
        return $pdf->download('current-year-annual-sales-report.pdf');
    }
    


public function downloadAllAnnualPdf()
{
    $user = auth()->user();
    $branchId = $user->branch_id; // Logged-in user's branch
    $selectedBranch = $user->branch->name ?? 'N/A'; // Branch name or fallback

    // Query to fetch annual reports
    $reportsQuery = Appointment::selectRaw('
            YEAR(appointments.date) as year,
            SUM(appointments.total) as total_sales,
            GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
        ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id') // Include branch details
        ->where('appointments.status', 1) // Only completed appointments
        ->groupByRaw('YEAR(appointments.date)')
        ->orderBy('year', 'desc');

    // Apply branch filter for employees
    if ($user->role_id == 2) { // Assuming role_id 2 is Employee
        $reportsQuery->where('employees.branch_id', $branchId);
    }

    $reports = $reportsQuery->get();

    // Transform report data to group services
    $reports->transform(function ($report) {
        $services = explode(',', $report->services_with_details);
        $groupedServices = collect($services)->map(function ($service) {
            [$name, $price, $employee, $customer] = explode(':', $service);
            return [
                'name' => $name,
                'price' => (float) $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        })->groupBy('name')->map(function ($group) {
            return [
                'total_price' => $group->sum('price'),
                'details' => $group,
            ];
        });

        $report->grouped_services = $groupedServices;
        return $report;
    });

    $grandTotal = $reports->sum('total_sales'); // Grand total of all reports

    // Image path and other details
    $image = file_exists(public_path('images/banner-purple.png'))
        ? public_path('images/banner-purple.png')
        : null; // Ensure the image exists to avoid errors
    $preparedBy = $user->name ?? 'System Admin'; // Default to "System Admin" if no user name
    $currentDateTime = now()->format('Y-m-d H:i:s'); // Timestamp for the report

    // Generate PDF with the report view
    $pdf = Pdf::loadView('reports.all-annual-pdf', [
        'reports' => $reports,
        'grandTotal' => $grandTotal,
        'preparedBy' => $preparedBy,
        'currentDateTime' => $currentDateTime,
        'image' => $image,
        'selectedBranch' => $selectedBranch,
    ]);

    return $pdf->download('all-annual-sales-report.pdf'); // Download the PDF
}


}
