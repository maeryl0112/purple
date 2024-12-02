<?php

namespace App\Http\Controllers;

use App\Enums\UserRolesEnum;
use App\Models\Appointment;
use App\Models\Deal;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Supply;
use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\DashboardHomeController;

use Illuminate\Http\Request;

class AdminDashboardHomeController extends Controller
{
    public function index()
    {
        $todayDate = Carbon::today()->toDateString();

        $totalCustomers = User::where('role_id', UserRolesEnum::Customer)->count();
        $totalEmployees = Employee::count();
        $totalServicesActive = Service::where('is_hidden', 0)->count();
        $totalServices = Service::count();

        $totalUpcomingAppointments = Appointment::where('status', 1)->count();


        $totalCompletedAppointments = Appointment::where('status', 2)->count();

        $bookingRevenueThisMonth = Appointment::where('created_at', '>', Carbon::today()->subMonth()->toDateTimeString())
            ->where('status', '!=', 0)
            ->sum('total');
        $bookingRevenueLastMonth = Appointment::where('created_at', '>', Carbon::today()->subMonths(2)->toDateTimeString())
            ->where('created_at', '<', Carbon::today()->subMonth()->toDateTimeString())
            ->where('status', '!=', 0)
            ->sum('total');

        $percentageRevenueChangeLastMonth = $bookingRevenueLastMonth != 0
            ? ($bookingRevenueThisMonth - $bookingRevenueLastMonth) / $bookingRevenueLastMonth * 100
            : 100;



      //  $timeSlots = TimeSlot::all(); //
        // Monthly revenue data for the chart
        $currentYear = Carbon::now()->year;

        $revenueData = DB::table('appointments')
        ->selectRaw('MONTH(date) as month, SUM(total) as total')
        ->whereYear('date', $currentYear)
        ->where('status', 2) // Only completed appointments
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Convert month numbers to full month names
        $months = $revenueData->pluck('month')->map(function ($month) {
            return Carbon::create()->month($month)->format('F'); // e.g., "December"
        });

        $totals = $revenueData->pluck('total');

        $selectedYear = request()->input('year', Carbon::now()->year);

        // Fetch the top 3 grossing services for the selected year
        $topGrossingServices = DB::table('appointments')
            ->select('services.name as service_name', DB::raw('SUM(appointments.total) as total_earnings'))
            ->join('services', 'appointments.service_id', '=', 'services.id') // Join with services table
            ->whereYear('appointments.date', $selectedYear) // Filter by selected year
            ->groupBy('services.name') // Group by service name
            ->orderByDesc('total_earnings') // Sort by earnings in descending order
            ->limit(3) // Limit to top 3
            ->get();


            $paymentBreakdown = DB::table('appointments')
            ->select('payment', DB::raw('COUNT(*) as payment_count'))
            ->where('status', 'completed') // Only include completed appointments
            ->groupBy('payment') // Group by payment method (Cash, Online)
            ->get();

        // Format the result as an associative array for easier use in the frontend
        $paymentData = $paymentBreakdown->pluck('payment_count', 'payment')->toArray();


        $lowQuantitySupplies = Supply::where('quantity', '<=', 5)->get();


        $expirationThreshold = 30; // days
        $nearExpirationSupplies = Supply::where('expiration_date', '<=', Carbon::today()->addDays($expirationThreshold))
            ->where('expiration_date', '>', $todayDate)
            ->with('online_supplier')
            ->get();

        $serviceCategoryRevenue = DB::table('appointments')
            ->select('categories.name as category_name', DB::raw('SUM(appointments.total) as total_revenue'))
            ->join('services', 'appointments.service_id', '=', 'services.id') // Join with services
            ->join('categories', 'services.category_id', '=', 'categories.id') // Join with categories
            ->where('appointments.status', 2) // Filter only completed appointments
            ->groupBy('categories.name') // Group by category name
            ->orderByDesc('total_revenue') // Sort by total revenue
            ->get();

        // Top Customers based on Appointment Revenue
        $topCustomers = DB::table('appointments')
        ->select(
            'users.name',
            'users.email',
            DB::raw('SUM(appointments.total) as total_revenue')
        )
        ->join('users', 'appointments.user_id', '=', 'users.id') // Join with users table
        ->where('appointments.status', '!=', 0) // Exclude canceled appointments
        ->groupBy('users.id', 'users.name', 'users.email') // Group by user fields
        ->orderByDesc('total_revenue') // Sort by total revenue
        ->limit(5) // Limit to the top 5 customers
        ->get();

        $todayDate = Carbon::today()->toDateString();
        $tomorrowDate = Carbon::today()->addDay()->toDateString();

        // Fetch today's schedule
        $todaysSchedule = Appointment::select(
            'appointments.*',
            'employees.first_name as employee_name',
            'services.name as service_name',
            'users.name as customer_name',
            'users.email as customer_email',
            'users.phone_number as customer_phone'
        )
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->where('appointments.date', $todayDate)
            ->where('appointments.status', 1) // Only upcoming appointments
            ->get();

        // Fetch tomorrow's schedule
        $tomorrowsSchedule = Appointment::select(
            'appointments.*',
            'employees.first_name as employee_name',
            'services.name as service_name',
            'users.name as customer_name',
            'users.email as customer_email',
            'users.phone_number as customer_phone'
        )
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->where('appointments.date', $tomorrowDate)
            ->where('appointments.status', 1) // Only upcoming appointments
            ->get();

        // Fetch upcoming schedule
        $upcomingSchedule = Appointment::select(
            'appointments.*',
            'employees.first_name as employee_name',
            'services.name as service_name',
            'users.name as customer_name',
            'users.email as customer_email',
            'users.phone_number as customer_phone'
        )
            ->leftJoin('employees', 'appointments.employee_id', '=', 'employees.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->leftJoin('users', 'appointments.user_id', '=', 'users.id')
            ->where('appointments.date', '>', $tomorrowDate)
            ->where('appointments.status', 1) // Only upcoming appointments
            ->orderBy('appointments.date')
            ->get();



        return view('dashboard.admin-employee', [
            'totalCustomers' => $totalCustomers,
            'totalEmployees' => $totalEmployees,
            'totalServicesActive' => $totalServicesActive,
            'totalServices' => $totalServices,
            'totalUpcomingAppointments' => $totalUpcomingAppointments,
            'totalCompletedAppointments' => $totalCompletedAppointments,
            'bookingRevenueThisMonth' => $bookingRevenueThisMonth,
            'bookingRevenueLastMonth' => $bookingRevenueLastMonth,
            'percentageRevenueChangeLastMonth' => $percentageRevenueChangeLastMonth,
                 'totals' => $totals,
            'months' => $months,
            'revenueData' => $revenueData,
            'lowQuantitySupplies' => $lowQuantitySupplies,
            'nearExpirationSupplies' => $nearExpirationSupplies,
            'topGrossingServices' => $topGrossingServices,
             'selectedYear' => $selectedYear,
             'serviceCategoryRevenue' => $serviceCategoryRevenue,
             'topCustomers' => $topCustomers,
             'paymentData' => $paymentData,
             'todaysSchedule' => $todaysSchedule,
        'tomorrowsSchedule' => $tomorrowsSchedule,
        'upcomingSchedule' => $upcomingSchedule,
            // Supplies nearing expiration

        ]);
    }
    public function generateReport()
    {
        $serviceCategoryRevenue = DB::table('appointments')
            ->select(
                'categories.name as category_name',
                'services.name as service_name',
                DB::raw('SUM(appointments.total) as service_revenue'),
                DB::raw('COUNT(appointments.id) as service_count'), // Count of appointments
                'services.price as service_price'
            )
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->join('categories', 'services.category_id', '=', 'categories.id')
            ->where('appointments.status', 2)
            ->groupBy('categories.name', 'services.name', 'services.price')
            ->orderBy('categories.name')
            ->orderByDesc('service_revenue')
            ->get()
            ->groupBy('category_name');

            $image = public_path('images/banner-purple.png');
            $preparedBy = auth()->user()->name ?? 'System Admin';
            $currentDateTime = now()->format('Y-m-d H:i:s');
        $pdf = PDF::loadView('reports.service_category_with_services', compact('serviceCategoryRevenue','currentDateTime','preparedBy','image'));

        return $pdf->download('service_category_with_services_report.pdf');
    }

    public function generateAllCustomersReport()
    {
        $customers = DB::table('appointments')
            ->select(
                'users.id as user_id',
                'users.name',
                'users.email',
                'services.name as service_name',
                'appointments.total as service_price',
                DB::raw('SUM(appointments.total) OVER (PARTITION BY users.id) as total_revenue')
            )
            ->join('users', 'appointments.user_id', '=', 'users.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', '!=', 0)
            ->orderByDesc('total_revenue')
            ->get()
            ->groupBy('user_id');

            $image = public_path('images/banner-purple.png');
            $preparedBy = auth()->user()->name ?? 'System Admin';
            $currentDateTime = now()->format('Y-m-d H:i:s');

        // Load the PDF view
        $pdf = PDF::loadView('reports.all_customers_with_services', compact('customers'.'currentDateTime','preparedBy','image'));

        // Return the PDF download
        return $pdf->download('all_customers_with_services_report.pdf');
    }


}
