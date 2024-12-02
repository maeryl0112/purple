<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    //daily sales report
    public function dailyReport()
    {
        $reports = Appointment::selectRaw('
        date,
        SUM(total) as total_sales,
        COUNT(appointments.id) as appointment_count,
        GROUP_CONCAT(DISTINCT CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
    ')
    ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
    ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
    ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users/customers table
    ->where('appointments.status', 2) // Assuming 2 means completed
    ->groupBy('date')
    ->get();

    // Process services with details into structured data
    $reports->transform(function ($report) {
        $services = explode(',', $report->services_with_details);
        $report->services_with_details = collect($services)->map(function ($service) {
            [$name, $price, $employee, $customer] = explode(':', $service);
            return [
                'name' => $name,
                'price' => $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        });
        return $report;
    });

    $grandTotal = $reports->sum('total_sales'); // Calculate grand total
    return view('reports.daily', compact('reports', 'grandTotal'));

    }

    //specific day
    public function downloadPDF()
{
    $today = now()->toDateString(); // Get today's date in 'Y-m-d' format

    $reports = Appointment::selectRaw('
        `date`,
        SUM(`total`) as total_sales,
        COUNT(appointments.id) as appointment_count,
        GROUP_CONCAT(DISTINCT CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as service_employee_customer_pairs
    ')
        ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
        ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
        ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users table
        ->where('appointments.status', 2) // Only completed appointments
        ->whereDate('appointments.date', $today) // Filter by today's date
        ->groupBy('date')
        ->get();

    // Transform the results to make them more structured
    $reports->transform(function ($report) {
        $pairs = explode(',', $report->service_employee_customer_pairs);
        $report->services_with_employees_and_customers = collect($pairs)->map(function ($pair) {
            [$service, $price, $employee, $customer] = explode(':', $pair);
            return [
                'service' => $service,
                'price' => $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        });
        return $report;
    });

        $image = public_path('images/banner-purple.png'); // Path to the banner image

        $grandTotal = $reports->sum('total_sales'); // Sum of all daily sales
        $preparedBy = auth()->user()->name ?? 'System Admin'; // Name of the user generating the report
        $currentDateTime = now()->format('Y-m-d H:i:s'); // Current date and time for the report

        // Generate the PDF
        $pdf = Pdf::loadView('reports.daily-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'grandTotal', 'image'));
        return $pdf->download('daily-sales-report-' . $today . '.pdf');
    }

    //download all daily
        public function downloadAllPDF()
        {
            $reports = Appointment::selectRaw('
        date,
        SUM(total) as total_sales,
        COUNT(appointments.id) as appointment_count,
        GROUP_CONCAT(DISTINCT CONCAT(services.name, ":", services.price, ":", employees.first_name, ":", users.name)) as services_with_details
    ')
    ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
    ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
    ->leftJoin('users', 'appointments.user_id', '=', 'users.id') // Join users/customers table
    ->where('appointments.status', 2) // Assuming 2 means completed
    ->groupBy('date')
    ->get();

    // Process services with details into structured data
    $reports->transform(function ($report) {
        $services = explode(',', $report->services_with_details);
        $report->services_with_details = collect($services)->map(function ($service) {
            [$name, $price, $employee, $customer] = explode(':', $service);
            return [
                'name' => $name,
                'price' => $price,
                'employee' => $employee,
                'customer' => $customer,
            ];
        });
        return $report;
    });

    $image = public_path('images/banner-purple.png');
    $grandTotal = $reports->sum('total_sales');
    $preparedBy = auth()->user()->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    $pdf = Pdf::loadView('reports.all-daily-pdf', compact('reports', 'preparedBy', 'currentDateTime', 'grandTotal', 'image'));

    return $pdf->download('all-daily-sales-reports.pdf');
}

    //weekly
    public function weeklyReport()
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

    $grandTotal = $reports->sum('total_sales');
    return view('reports.weekly', compact('reports', 'grandTotal'));
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


    public function monthlyReport()
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

        // Calculate the grand total for the year
        $grandTotal = $reports->sum('total_sales');

        return view('reports.monthly', compact('reports', 'grandTotal'));
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


    public function quarterlyReport()
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

    return view('reports.quarterly', compact('reports'));
}


public function annualReport()
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

    return view('reports.annual', compact('reports'));
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
