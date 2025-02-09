<?php

namespace App\Http\Controllers;

use App\Enums\UserRolesEnum;
use App\Models\Appointment;
use App\Models\Deal;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Supply;
use App\Models\Equipment;
use App\Models\Branch;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\DashboardHomeController;

use Illuminate\Http\Request;

class AdminDashboardHomeController extends Controller
{
    public function index(Request $request)
{
    $todayDate = Carbon::today()->toDateString();
    $tomorrowDate = Carbon::tomorrow()->toDateString();
    $currentYear = Carbon::now()->year;

        $loggedInUser = auth()->user();
    
        if ($loggedInUser->role_id === 1) {
            // Admin can see all appointments
            $lowQuantitySupplies = Supply::where('quantity', '<=', 5)->get();

            $expirationThreshold = 7; // days
            $nearExpirationSupplies = Supply::where('expiration_date', '<=', Carbon::today()->addDays($expirationThreshold))
                ->where('expiration_date', '>', Carbon::today())
                ->with('online_supplier')
                ->get();
            $totalUpcomingAppointments = Appointment::where('status', 1)->count();
            $totalCompletedAppointments = Appointment::where('status', 0)->count();
            $totalEmployees = Employee::count();
            $totalServices = Service::count();
            $totalCustomers = User::where('role_id', UserRolesEnum::Customer)->count();
            $totalServicesActive = Service::where('status', 1)->count();
        } else {
           
            // Employee can only see data for their branch
            $branchId = $loggedInUser->branch_id;

            $lowQuantitySupplies = Supply::where('quantity', '<=', 5)
            ->where('branch_id', $loggedInUser->branch_id)
            ->get();
    
            $expirationThreshold = 7; // days
            $nearExpirationSupplies = Supply::where('expiration_date', '<=', Carbon::today()->addDays($expirationThreshold))
                ->where('expiration_date', '>', Carbon::today())
                ->where('branch_id', $loggedInUser->branch_id)
                ->with('online_supplier')
                ->get();
    
            $totalUpcomingAppointments = Appointment::where('status', 1)
                ->whereHas('employee', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
    
            $totalCompletedAppointments = Appointment::where('status', 0)
                ->whereHas('employee', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->count();
    
            $totalEmployees = Employee::where('branch_id', $branchId)->count();
    
            // Get total services linked to this branch
            $totalServices = DB::table('branch_service')
                ->where('branch_id', $branchId)
                ->count();
    
            // Get total active services (instead of supplies)
            $totalServicesActive = Service::where('status', 1)
                ->whereExists(function ($query) use ($branchId) {
                    $query->select(DB::raw(1))
                        ->from('branch_service')
                        ->whereColumn('branch_service.service_id', 'services.id')
                        ->where('branch_service.branch_id', $branchId);
                })
                ->count();
    
            // Count customers in this branch (fixing previous mistake)
            $totalCustomers = Equipment::where('branch_id', $branchId)->count();
                
        }
    
        $topCustomers = DB::table('appointments')
        ->select(
            'users.id',
            'users.name',
            'users.email',
            DB::raw('SUM(appointments.total) as total_revenue')
        )
        ->join('users', 'appointments.user_id', '=', 'users.id') // Join with users table
        ->join('employees', 'appointments.employee_id', '=', 'employees.id') // Join with employees table
        ->whereIn('appointments.status', [1]); 

        // Initialize the service revenue query before applying branch filters
           

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
                ->where('appointments.status', 1); // Only upcoming appointments
            
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
                ->where('appointments.status', 1);

            $revenueQuery = DB::table('appointments')
                ->selectRaw('MONTH(date) as month, SUM(total) as total')
                ->whereYear('date', $currentYear)
                ->where('appointments.status', 1 ) // Only completed appointments
                ->groupBy('month')
                ->orderBy('month');

                
                $serviceCategoryRevenueQuery = DB::table('appointments')
                ->select(
                    'categories.name as category_name',
                    'services.name as service_name',
                    DB::raw('SUM(appointments.total) as total_revenue')
                )
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->join('categories', 'services.category_id', '=', 'categories.id')
                ->join('employees', 'appointments.employee_id', '=', 'employees.id') // Ensure employee linkage
                ->where('appointments.status', 1)
                ->groupBy('categories.name', 'services.name')
                ->orderByDesc('total_revenue');

        if ($loggedInUser->role_id === 2) {
            $serviceCategoryRevenueQuery->where('employees.branch_id', $loggedInUser->branch_id);
            $topCustomers->where('employees.branch_id', $loggedInUser->branch_id);
            $todaysSchedule->where('employees.branch_id', $loggedInUser->branch_id);
            $tomorrowsSchedule->where('employees.branch_id', $loggedInUser->branch_id);
            $revenueQuery->join('employees', 'appointments.employee_id', '=', 'employees.id')
            ->where('employees.branch_id', $loggedInUser->branch_id);
        }

       // ✅ Execute query first
            $serviceCategoryRevenue = $serviceCategoryRevenueQuery->get();

            $structuredData = []; // Initialize array

            foreach ($serviceCategoryRevenue as $item) { // ✅ Now it's a collection, safe to loop
                // ✅ Ensure `category_name` is always a string (even if null)
                $categoryKey = isset($item->category_name) && is_string($item->category_name)
                    ? $item->category_name
                    : 'No data'; // Default if category is missing

                // ✅ Store data properly
                $structuredData[$categoryKey][] = [
                    'service_name' => $item->service_name ?? 'Unknown Service',
                    'total_revenue' => $item->total_revenue ?? 0,
                ];
            }

            $topCustomers = $topCustomers
            ->groupBy('users.id', 'users.name', 'users.email') // Group by user fields
            ->orderByDesc('total_revenue') // Sort by total revenue
            ->limit(5) // Limit to the top 5 customers
            ->get();

            
            // Get the final results
// Ensure it's a collection before processing
        $todaysSchedule = $todaysSchedule->get();
       
        $tomorrowsSchedule = $tomorrowsSchedule->get();

        $revenueData = $revenueQuery->get();

        // Convert month numbers to full month names
        $months = $revenueData->pluck('month')->map(function ($month) {
            return Carbon::createFromDate(2000, $month, 1)->format('F'); // e.g., "January"
        });

        $totals = $revenueData->pluck('total');
     

      
        $selectedYear = request()->input('year', Carbon::now()->year);



      

     


      


            

        return view('dashboard.admin-employee', [
            'totalCustomers' => $totalCustomers,
            'totalEmployees' => $totalEmployees,
            'totalServicesActive' => $totalServicesActive,
            'totalServices' => $totalServices,
            'totalUpcomingAppointments' => $totalUpcomingAppointments,
            'totalCompletedAppointments' => $totalCompletedAppointments,
           
                 'totals' => $totals,
                 
            'months' => $months,
            'revenueData' => $revenueData,
            'lowQuantitySupplies' => $lowQuantitySupplies,
            'nearExpirationSupplies' => $nearExpirationSupplies,
             'selectedYear' => $selectedYear,
             'serviceCategoryRevenue' => $serviceCategoryRevenue,
             'topCustomers' => $topCustomers,
             'todaysSchedule' => $todaysSchedule,
        'tomorrowsSchedule' => $tomorrowsSchedule,
        'structuredData' => $structuredData,


        ]);
    }

    public function generateReport()
{
    $loggedInUser = auth()->user();

    // Get branch name for employees (if not admin)
    $assignedBranch = 'All Branches';
    if ($loggedInUser->role_id !== 1) {
        $assignedBranch = Branch::where('id', $loggedInUser->branch_id)->value('name');
    }

    $serviceCategoryRevenue = DB::table('appointments')
        ->select(
            'categories.name as category_name',
            'services.name as service_name',
            'branches.name as branch_name',
            DB::raw('SUM(appointments.total) as service_revenue'),
            DB::raw('COUNT(appointments.id) as service_count'),
            'services.price as service_price'
        )
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->join('categories', 'services.category_id', '=', 'categories.id')
        ->join('employees', 'appointments.employee_id', '=', 'employees.id')
        ->join('branches', 'employees.branch_id', '=', 'branches.id');

    // Apply branch filter for employees (not admins)
    if ($loggedInUser->role_id !== 1) {
        $serviceCategoryRevenue->where('employees.branch_id', $loggedInUser->branch_id);
    }

    $serviceCategoryRevenue = $serviceCategoryRevenue
        ->where('appointments.status', 1)
        ->groupBy('categories.name', 'services.name', 'services.price', 'branches.name')
        ->orderBy('categories.name')
        ->orderByDesc('service_revenue')
        ->get()
        ->groupBy('category_name');

    $image = public_path('images/banner-purple.png');
    $preparedBy = $loggedInUser->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    $pdf = PDF::loadView('reports.service_category_with_services', compact(
        'serviceCategoryRevenue',
        'currentDateTime',
        'preparedBy',
        'image',
        'assignedBranch'
    ));

    return $pdf->download('service_category_with_services_report.pdf');
}

    
    

public function generateAllCustomersReport()
{
    $loggedInUser = auth()->user();

    // Default: Admin sees all branches
    $assignedBranch = 'All Branches';
    
    // Employees see only their branch
    if ($loggedInUser->role_id !== 1) {
        $assignedBranch = Branch::where('id', $loggedInUser->branch_id)->value('name');
    }

    $customers = DB::table('appointments')
        ->select(
            'users.id as user_id',
            'users.name',
            'users.email',
            'services.name as service_name',
            'appointments.total as service_price',
            DB::raw('SUM(appointments.total) as total_revenue'),
            'branches.name as branch_name' // Include branch name
        )
        ->join('users', 'appointments.user_id', '=', 'users.id')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->join('employees', 'appointments.employee_id', '=', 'employees.id')
        ->join('branches', 'employees.branch_id', '=', 'branches.id')
        ->where('appointments.status', 1);

    // Apply branch filter for employees
    if ($loggedInUser->role_id !== 1) {
        $customers->where('employees.branch_id', $loggedInUser->branch_id);
    }

    $customers = $customers
        ->groupBy('users.id', 'users.name', 'users.email', 'services.name', 'appointments.total', 'branches.name')
        ->orderByDesc('total_revenue')
        ->get()
        ->groupBy('user_id');

    $image = public_path('images/banner-purple.png');
    $preparedBy = $loggedInUser->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    $pdf = PDF::loadView('reports.all_customers_with_services', compact(
        'customers', 
        'currentDateTime',
        'preparedBy',
        'image',
        'assignedBranch'
    ));

    return $pdf->download('all_customers_with_services.pdf');
}

}
