<?php

use App\Http\Controllers;
use App\Http\Controllers\AdminController;
use App\Enums\UserRolesEnum;
use App\Http\Controllers\AdminDashboardHomeController;
use App\Models\Role;
use App\Http\Controllers\UserSuspensionController;
use App\Http\Controllers\DiscountCodeController;
use App\Http\Controllers\DisplayContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\SalesReportController;
use App\Models\Payment;
use App\Services\TwilioService;
use Illuminate\Support\Facades\DB;






/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/test', [App\Http\Controllers\AdminDashboardHome::class, 'index'])->name('test');


Route::get('/', [App\Http\Controllers\HomePageController::class, 'index'])->name('home');
Route::get('/about',[App\Http\Controllers\DisplayAbout::class, 'about'])->name('about');
Route::get('/services', [App\Http\Controllers\DisplayService::class, 'index'])->name('services');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
// Users needs to be logged in for these routes

Route::prefix('dashboard')->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardHomeController::class, 'index'])->name('dashboard');

});




Route::get('/services/{slug}', [App\Http\Controllers\DisplayService::class, 'show'])->name('view-service');


        // middleware to give access only for admin
        Route::middleware([
            'validateRole:Admin'
        ])->group(function () {

            Route::prefix('manage')->group( function () {




                Route::get('employees', function () {
                    return view('dashboard.manage-employees.index');
                })->name('manageemployees');

                Route::get('admin/discount_code/list',[DiscountCodeController::class,'list'])->name('managediscountocodes');
                Route::get('admin/discount_code/add',[DiscountCodeController::class, 'add'])->name('managediscountcode.add');
                Route::post('admin/discount_code/add',[DiscountCodeController::class, 'insert'])->name('managediscountcode.insert');
                Route::get('admin/discount_code/edit/{$id}',[DiscountCodeController::class, 'edit'])->name('managediscountcode.edit');
                Route::post('admin/discount_code/edit/{$id}',[DiscountCodeController::class, 'update'])->name('managediscountcode.update');
                Route::get('admin/discount_code/delete/{$id}',[DiscountCodeController::class, 'delete'])->name('managediscountcode.delete');




            });



        });

        // middlleware to give access only for admin and employee
        Route::middleware([
            'validateRole:Admin,Employee', 
            'branch.access' // Apply branch restriction middleware
        ])->group(function () {

            Route::post('/notifications/mark-as-read/{id}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

            Route::get('/notifications/redirect/{id}', [App\Http\Controllers\NotificationController::class, 'redirectToAppointment'])->name('notifications.redirectToAppointment');



            Route::get('/admin/reports/top-customers', [AdminDashboardHomeController::class, 'index']);

            Route::get('/inventory-history', [App\Http\Controllers\EquipmentNotification::class, 'showNotifications'])->name('inventory.notification');

            Route::get('/daily-report', [SalesReportController::class, 'dailyReport'])->name('daily.report');
            Route::get('/daily-report/pdf', [SalesReportController::class, 'downloadPDF'])->name('daily.report.pdf');
            Route::get('/all-daily-report/pdf', [SalesReportController::class, 'downloadAllPDF'])->name('all.daily.report.pdf');


            Route::get('/weekly-report', [SalesReportController::class, 'weeklyReport'])->name('weekly.report');
            Route::get('/weekly-report/pdf', [SalesReportController::class, 'downloadWeeklyPDF'])->name('weekly.report.pdf');
            Route::get('/all-weekly-report/pdf', [SalesReportController::class, 'downloadAllWeeklyPDF'])->name('all.weekly.report.pdf');

            Route::get('/monthly-report', [SalesReportController::class, 'monthlyReport'])->name('monthly.report');
            Route::get('/monthly-report/pdf', [SalesReportController::class, 'downloadMonthlyPDF'])->name('monthly.report.pdf');
            Route::get('/all-monthly-report/pdf', [SalesReportController::class, 'downloadAllMonthlyPDF'])->name('all.monthly.report.pdf');

            Route::get('/quarterly-report', [SalesReportController::class, 'quarterlyReport'])->name('quarterly.report');
            Route::get('/quarterly-report/pdf', [SalesReportController::class, 'downloadQuarterlyPDF'])->name('quarterly.report.pdf');
            Route::get('/all-quarterly-report/pdf', [SalesReportController::class, 'downloadAllQuarterlyPDF'])->name('all.quarterly.report.pdf');


            Route::get('/annual-report', [SalesReportController::class, 'annualReport'])->name('annual.report');
            Route::get('/annual-report/pdf', [SalesReportController::class, 'downloadAnnualPDF'])->name('annual.report.pdf');
            Route::get('/all-annual-report/pdf', [SalesReportController::class, 'downloadAllAnnualPDF'])->name('all.annual.report.pdf');

            Route::get('/generate-service-category-revenue-report', [AdminDashboardHomeController::class, 'generateReport'])->name('category.pdf');
            Route::get('/generate-top-customers-with-services-report', [AdminDashboardHomeController::class, 'generateAllCustomersReport'])->name('top.customer.pdf');


            Route::prefix('manage')->group( function () {
                Route::resource('users', UserController::class)->names([
                    'index' => 'manageusers',         // GET /users (index)
                    'store' => 'manageusers.store',   // POST /users (store)
                    'create' => 'manageusers.create', // GET /users/create (create)
                    'edit' => 'manageusers.edit',     // GET /users/{id}/edit (edit)
                    'update' => 'manageusers.update', // PUT /users/{id} (update)
                    'destroy' => 'manageusers.destroy'// DELETE /users/{id} (destroy)
                ]);
                Route::put('users/{id}/suspend', [UserSuspensionController::class, 'suspend'])->name('manageusers.suspend');
                Route::put('users/{id}/activate', [UserSuspensionController::class, 'activate'])->name('manageusers.activate');

                Route::get('services', function () {
                    return view('dashboard.manage-services.index');
                })->name('manageservices');

                Route::get('deals', function () {
                    return view('dashboard.manage-deals.index');
                })->name('managedeals');

                Route::get('concerns', function () {
                    return view('livewire.manage-concern');
                })->name('manageconcerns');

                Route::get('holidays', function () {
                    return view('dashboard.manage-holidays.index');
                })->name('manageholidays');

                Route::get('categories', function () {
                    return view('dashboard.manage-categories.index');
                })->name('managecategories' );

                Route::get('equipments', function () {
                    return view('dashboard.manage-equipments.index');
                })->name('manageequipments' );

                Route::get('appointments', function () {
                    return view('dashboard.manage-appointments.index');
                })->name('manageappointments');

                Route::get('onlinesupplier', function () {
                    return view('dashboard.manage-online-suppliers.index');
                })->name('manageonlinesuppliers');

                Route::get('supplies', function () {
                    return view('dashboard.manage-supplies.index');
                })->name('managesupplies');

                Route::get('jobcategories', function () {
                    return view('dashboard.manage-job-categories.index');
                })->name('managejobcategories' );

                Route::get('jobcategories/create', function () {
                    return view('dashboard.manage-job-categories.index');
                })->name('managejobcategories.create');

                Route::get('sales-report', function(){
                    return view('dashboard.sales-report.index');
                })->name('salesreport');

                Route::get('payments', function(){
                    return view('dashboard.manage-payments.index');
                })->name('managepayments');

                Route::get('branches', function(){
                    return view('dashboard.manage-branches.index');
                })->name('managebranches');
            });

        });

        Route::middleware([
            'validateRole:Customer'
        ])->group(function () {

            Route::prefix('cart')->group( function () {
                Route::get('/', [App\Http\Controllers\CartController::class, 'index'])->name('cart');
                Route::post('/', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
                Route::delete('/item/{cart_service_id}', [App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.remove-item');
                Route::delete('/{id}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
                Route::post('/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('cart.checkout');
                Route::get('/get-qr-code', function () {
                    $payment = DB::table('payments')->latest('created_at')->first();

                    if ($payment) {
                        return response()->json(['qrImage' => asset('storage/' . $payment->image)]);
                    }

                    return response()->json(['qrImage' => null], 404);
                })->name('get.qr.code');
            });

        });
    });
