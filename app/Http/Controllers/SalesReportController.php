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
                // Get the date and branch ID from the request, default to today and all branches
                $selectedDate = $request->input('date', now()->toDateString());
                $selectedBranch = $request->input('branch_id'); // Optional branch filter

                // Query data for the selected date with an optional branch filter
                $reports = Appointment::selectRaw('
                        appointments.date,
                        SUM(appointments.total) as total_sales,
                        COUNT(appointments.id) as appointment_count,
                        GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
                    ')
                    ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
                    ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
                    ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users/customers table
                    ->leftJoin('branches', 'appointments.branch_id', '=', 'branches.id') // Join branches table
                    ->where('appointments.status', 1) // Assuming 2 means completed
                    ->whereDate('appointments.date', $selectedDate) // Filter by the selected date
                    ->when($selectedBranch, function ($query, $selectedBranch) {
                        return $query->where('appointments.branch_id', $selectedBranch);
                    })
                    ->groupBy('appointments.date')
                    ->get();

                // Process services_with_details into structured data
                $reports->transform(function ($report) {
                    $services = explode(',', $report->services_with_details);

                    // Group services by name and calculate totals
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

                // Fetch all branches for dropdown
                $branches = \App\Models\Branch::all();

                return view('reports.daily', compact('reports', 'grandTotal', 'selectedDate', 'selectedBranch', 'branches'));
            }

    
    // Specific Day Report
    public function downloadPDF(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString()); // Get selected or today's date
        $selectedBranch = $request->input('branch_id'); // Get selected branch
        $today = now()->toDateString();
    
        $reports = Appointment::selectRaw('
                appointments.date,
                SUM(appointments.total) as total_sales,
                COUNT(appointments.id) as appointment_count,
                GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->leftJoin('branches', 'appointments.branch_id', '=', 'branches.id')
            ->where('appointments.status', 1)
            ->whereDate('appointments.date', $selectedDate) // Filter by selected date
            ->when($selectedBranch, function ($query, $selectedBranch) {
                return $query->where('appointments.branch_id', $selectedBranch);
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
    
        $image = public_path('images/banner-purple.png');
        $grandTotal = $reports->sum('total_sales');
        $preparedBy = auth()->user()->name ?? 'System Admin';
        $currentDateTime = now()->format('Y-m-d H:i:s');
    
        // Generate the PDF
        $pdf = Pdf::loadView('reports.daily-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'grandTotal', 'image', 'selectedDate', 'selectedBranch'));
    
        return $pdf->download('daily-sales-report-' . $selectedDate . '.pdf');
    }
    
// All Daily Reports
public function downloadAllPDF()
{
    $reports = Appointment::selectRaw('
            appointments.date,
            SUM(appointments.total) as total_sales,
            COUNT(appointments.id) as appointment_count,
            GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
        ->leftJoin('branches', 'appointments.branch_id', '=', 'branches.id') // Include branches
        ->where('appointments.status', 1) // Only completed appointments
        ->groupBy('appointments.date')
        ->orderBy('appointments.date', 'desc')
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
    $preparedBy = auth()->user()->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    // Generate the PDF
    $pdf = Pdf::loadView('reports.all-daily-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'grandTotal', 'image'));

    return $pdf->download('all-daily-sales-reports.pdf');
}



    //weekly
    public function weeklyReport(Request $request)
    {
        // Get the selected week (format: YYYY-WW), default to the current week
        $selectedWeek = $request->input('week', Carbon::now()->format('Y-\WW'));
        $selectedBranch = $request->input('branch_id'); // Optional branch filter
    
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
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->when($selectedBranch, function ($query, $selectedBranch) {
                return $query->where('appointments.branch_id', $selectedBranch);
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
    
        // If request is AJAX, return JSON response for dynamic UI updates
        if ($request->ajax()) {
            $html = view('reports.weekly', compact('reports', 'grandTotal'))->render();
            return response()->json(['html' => $html]);
        }
    
        // Fetch all branches for filtering
        $branches = \App\Models\Branch::all();
    
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
    $selectedBranch = $request->input('branch_id');

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
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users/customers table
        ->leftJoin('branches', 'appointments.branch_id', '=', 'branches.id') // Join branches table
        ->where('appointments.status', 1) // Assuming 1 means completed
        ->whereYear('appointments.date', $year) // Filter by year
        ->whereMonth('appointments.date', $month) // Filter by month
        ->when($selectedBranch, function ($query, $selectedBranch) {
            return $query->where('appointments.branch_id', $selectedBranch);
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
    $branches = \App\Models\Branch::all();

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
    $selectedYear = $request->input('year', now()->year); // Default to the current year
    $selectedQuarter = $request->input('quarter', ceil(now()->month / 3)); // Default to the current quarter
    $selectedBranch = $request->input('branch_id'); // Optional branch filter

    $reports = Appointment::selectRaw('
            QUARTER(appointments.date) as quarter,
            YEAR(appointments.date) as year,
            SUM(appointments.total) as total_sales,
            COUNT(appointments.id) as appointment_count,
            GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
        ->leftJoin('branches', 'appointments.branch_id', '=', 'branches.id')
        ->where('appointments.status', 1)
        ->whereYear('appointments.date', $selectedYear)
        ->whereRaw('QUARTER(appointments.date) = ?', [$selectedQuarter])
        ->when($selectedBranch, function ($query, $selectedBranch) {
            return $query->where('appointments.branch_id', $selectedBranch);
        })
        ->groupBy('quarter', 'year')
        ->get();

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
    $branches = \App\Models\Branch::all();

    return view('reports.quarterly', compact('reports', 'grandTotal', 'selectedYear', 'selectedQuarter', 'selectedBranch', 'branches'));
}



public function annualReport(Request $request)
{
    $selectedYear = $request->input('year', now()->year); // Default to the current year
    $selectedBranch = $request->input('branch_id'); // Optional branch filter

    $reports = Appointment::selectRaw('
            YEAR(appointments.date) as year,
            SUM(appointments.total) as total_sales,
            COUNT(appointments.id) as appointment_count,
            GROUP_CONCAT(CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
        ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
        ->leftJoin('branches', 'appointments.branch_id', '=', 'branches.id')
        ->where('appointments.status', 1)
        ->whereYear('appointments.date', $selectedYear)
        ->when($selectedBranch, function ($query, $selectedBranch) {
            return $query->where('appointments.branch_id', $selectedBranch);
        })
        ->groupBy('year')
        ->get();

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
    $branches = \App\Models\Branch::all();

    return view('reports.annual', compact('reports', 'grandTotal', 'selectedYear', 'selectedBranch', 'branches'));
}



public function downloadQuarterlyPDF()
{
    // Get the current year and quarter
    $currentYear = now()->year; // Current year
    $currentQuarter = now()->quarter; // Current quarter (1, 2, 3, or 4)

    // Fetch the reports for the current quarter
    $reports = Appointment::selectRaw('
                QUARTER(appointments.date) as quarter,
                YEAR(appointments.date) as year,
                CONCAT("Q", QUARTER(appointments.date), " ", YEAR(appointments.date)) as quarter_label,
                MIN(appointments.date) as quarter_start,
                MAX(appointments.date) as quarter_end,
                SUM(appointments.total) as total_sales,
                COUNT(appointments.id) as appointment_count,  -- Count appointments
                COUNT(DISTINCT services.id) as service_count, -- Count distinct services
                GROUP_CONCAT(DISTINCT services.name) as services,
                GROUP_CONCAT(DISTINCT services.price) as prices,
                GROUP_CONCAT(DISTINCT employees.first_name) as employees,
                GROUP_CONCAT(DISTINCT users.name) as customers
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')   // Join with services
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')  // Join with users/customers
            ->where('appointments.status', 2) // Assuming 2 means completed
            ->whereYear('appointments.date', $currentYear) // Filter by current year
            ->whereRaw('QUARTER(appointments.date) = ?', [$currentQuarter]) // Filter by current quarter
            ->groupBy('year', 'quarter', 'quarter_label') // Correct grouping
            ->orderBy('year', 'DESC')
            ->orderBy('quarter', 'DESC')
            ->get();

    // Prepare additional data for the PDF view
    $image = public_path('images/banner-purple.png');
    $preparedBy = auth()->user()->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    // Load the PDF view with the data
    $pdf = Pdf::loadView('reports.quarterly-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'image'));

    // Download the PDF with a dynamic filename based on the current quarter and year
    return $pdf->download('quarterly-sales-report-Q' . $currentQuarter . '-' . $currentYear . '.pdf');
}

    public function downloadAllQuarterlyPDF()
    {
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

    public function downloadAnnualPDF()
    {
        $reports = Appointment::selectRaw('
                YEAR(`date`) as year,
                MIN(`date`) as year_start,
                MAX(`date`) as year_end,
                SUM(`total`) as total_sales,
                COUNT(appointments.id) as appointment_count,
                COUNT(DISTINCT services.id) as services_count,
                GROUP_CONCAT(DISTINCT services.name) as services,
                GROUP_CONCAT(DISTINCT services.price) as prices,
                GROUP_CONCAT(DISTINCT employees.first_name) as employees,
                GROUP_CONCAT(DISTINCT users.name) as customers
            ')
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')   // Join with services
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')           // Join with customers
            ->where('appointments.status', 2)  // Assuming 2 means completed
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->get();

        $image = public_path('images/banner-purple.png'); // Path to banner image
        $preparedBy = auth()->user()->name ?? 'System Admin'; // Name of the logged-in user or default
        $currentDateTime = now()->format('Y-m-d H:i:s'); // Current timestamp for the report

        $pdf = Pdf::loadView('reports.annual-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'image'));
        return $pdf->download('annual-sales-report.pdf');
    }


public function downloadAllAnnualPdf()
{
    $reports = Appointment::selectRaw('
    YEAR(`date`) as year,
    MIN(`date`) as year_start,
    MAX(`date`) as year_end,
    SUM(`total`) as total_sales,
    COUNT(appointments.id) as appointment_count,
    COUNT(DISTINCT services.id) as services_count,
    GROUP_CONCAT(DISTINCT services.name) as services,
    GROUP_CONCAT(DISTINCT services.price) as prices,
    GROUP_CONCAT(DISTINCT employees.first_name) as employees,
    GROUP_CONCAT(DISTINCT users.name) as customers
')
->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees
->leftJoin('services', 'appointments.service_id', '=', 'services.id')   // Join with services
->leftJoin('users', 'appointments.user_id', '=', 'users.id')           // Join with customers
->where('appointments.status', 2)  // Assuming 2 means completed
->groupBy('year')
->orderBy('year', 'DESC')
->get();

$image = public_path('images/banner-purple.png'); // Path to banner image
        $preparedBy = auth()->user()->name ?? 'System Admin'; // Name of the logged-in user or default
        $currentDateTime = now()->format('Y-m-d H:i:s'); // Current timestamp for the report

        $pdf = Pdf::loadView('reports.all-annual-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'image'));
        return $pdf->download('all-annual-sales-report.pdf');
}

}
