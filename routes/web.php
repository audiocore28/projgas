<?php

// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClientController;
// use App\Http\Controllers\ClientPaymentController;
use App\Http\Controllers\BatangasPaymentDetailController;
use App\Http\Controllers\MindoroPaymentDetailController;
// use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MindoroTransactionController;
use App\Http\Controllers\MonthlyMindoroTransactionController;
use App\Http\Controllers\BatangasTransactionController;
use App\Http\Controllers\MonthlyBatangasTransactionController;
use App\Http\Controllers\HelperController;
// use App\Http\Controllers\CashierController;
// use App\Http\Controllers\PumpAttendantController;
// use App\Http\Controllers\OfficeStaffController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
// use App\Http\Controllers\StatementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\AccountController;
// use App\Http\Controllers\StationController;
// use App\Http\Controllers\PumpController;
// use App\Http\Controllers\CompanyController;
// use App\Http\Controllers\StationTransactionController;
// use App\Http\Controllers\PumpReadingController;
// use App\Http\Controllers\SaleController;
// use App\Http\Controllers\CompanyValeController;
// use App\Http\Controllers\CalibrationController;
// use App\Http\Controllers\DiscountController;
use App\Http\Controllers\TankerController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Jetstream Dashboard
// Route::middleware(['auth:sanctum', 'verified'])->get('/jetdashboard', function () {
//     return Inertia\Inertia::render('Dashboard');
// })->name('jetdashboard');

// Auth
// Route::get('login', [LoginController::class, 'showLoginForm'])
//     ->name('login')
//     ->middleware('guest');

// Route::post('login', [LoginController::class, 'login'])
//     ->name('login.attempt')
//     ->middleware('guest');

// Route::post('logout', [LoginController::class, 'logout'])
//     ->name('logout');

 // Auth
 Route::get('login', [AuthenticatedSessionController::class, 'create'])
     ->name('login')
     ->middleware('guest');

 Route::post('login', [AuthenticatedSessionController::class, 'store'])
     ->name('login.store')
     ->middleware('guest');

 Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
     ->name('logout');


Route::group(['middleware' => 'auth'], function() {
	// Dashboard
	// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// Purchases
	Route::get('purchases/prints', [PurchaseController::class, 'prints'])->name('purchases.prints');

	Route::get('purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print')->middleware('can:print,purchase');

	Route::resource('purchases', PurchaseController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	// Mindoro Transaction
	Route::resource('mindoro-transactions', MindoroTransactionController::class)->only('edit');

	// Monthly Mindoro Transaction
	Route::resource('monthly-mindoro-transactions', MonthlyMindoroTransactionController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	Route::get('monthly-mindoro-transactions/{monthlyMindoroTransaction}/print', [MonthlyMindoroTransactionController::class, 'print'])->name('monthly-mindoro-transactions.print')->middleware('can:print,monthlyMindoroTransaction');

	// Batangas Transaction
	Route::resource('batangas-transactions', BatangasTransactionController::class)->only('edit');

	// Monthly Batangas Transaction
	Route::resource('monthly-batangas-transactions', MonthlyBatangasTransactionController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	Route::get('monthly-batangas-transactions/{monthlyBatangasTransaction}/print', [MonthlyBatangasTransactionController::class, 'print'])->name('monthly-batangas-transactions.print')->middleware('can:print,monthlyBatangasTransaction');

	// Suppliers
	Route::put('suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore')->middleware('can:restore,supplier');
	Route::resource('suppliers', SupplierController::class)->only([
		'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
	]);

	// Depots
	Route::put('depots/{depot}/restore', [DepotController::class, 'restore'])->name('depots.restore')->middleware('can:restore,depot');
	Route::resource('depots', DepotController::class)->only([
		'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
	]);

	// Accounts
	Route::put('accounts/{account}/restore', [AccountController::class, 'restore'])->name('accounts.restore')->middleware('can:restore,account');
	Route::resource('accounts', AccountController::class)->only([
		'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
	]);

	// // Stations
	// Route::put('stations/{station}/restore', [StationController::class, 'restore'])->name('stations.restore');
	// Route::resource('stations', StationController::class);

	// // Pumps
	// Route::put('pumps/{pump}/restore', [PumpController::class, 'restore'])->name('pumps.restore');
	// Route::resource('pumps', PumpController::class)->only(['store', 'destroy']);

	// // Companies
	// Route::put('companies/{company}/restore', [CompanyController::class, 'restore'])->name('companies.restore');
	// Route::resource('companies', CompanyController::class);

	// Clients
	Route::put('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore')->middleware('can:restore,client');
	Route::resource('clients', ClientController::class);

	// // Client Payments
	// Route::put('client-payments/{clientPayment}/restore', [ClientPaymentController::class, 'restore'])->name('client-payments.restore')->middleware('can:restore,clientPayment');
	// Route::resource('client-payments', ClientPaymentController::class);

	// Client Payment Details
	// Route::put('client-payment-details/{clientPaymentDetail}/restore', [ClientPaymentDetailController::class, 'restore'])->name('client-payment-details.restore')->middleware('can:restore,clientPaymentDetail');
	// Route::resource('client-payment-details', ClientPaymentDetailController::class);

	// Batangas Payment Details
	Route::get('clients/{client}/batangas-soa', [BatangasPaymentDetailController::class, 'edit'])->name('batangas-payment-details.edit')->middleware('can:viewPayment,client');
	Route::put('clients/{client}/batangas-soa', [BatangasPaymentDetailController::class, 'update'])->name('batangas-payment-details.update')->middleware('can:updatePayment,client');

	// Mindoro Payment Details
	Route::get('clients/{client}/mindoro-soa', [MindoroPaymentDetailController::class, 'edit'])->name('mindoro-payment-details.edit')->middleware('can:viewPayment,client');
	Route::put('clients/{client}/mindoro-soa', [MindoroPaymentDetailController::class, 'update'])->name('mindoro-payment-details.update')->middleware('can:updatePayment,client');


	// Statements
	// Route::put('statements/{statement}/restore', [StatementController::class, 'restore'])->name('statements.restore');
	// Route::resource('statements', StatementController::class);

	// Users
	Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore');
	Route::resource('users', UsersController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	// Roles
	Route::put('roles/{role}/restore', [RoleController::class, 'restore'])->name('roles.restore');
	Route::resource('roles', RoleController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	// Permissions
	Route::put('permissions/{permission}/restore', [PermissionController::class, 'restore'])->name('permissions.restore');
	Route::resource('permissions', PermissionController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	// Products
	Route::put('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore')->middleware('can:restore,product');
	Route::resource('products', ProductController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	// Tankers
	Route::put('tankers/{tanker}/restore', [TankerController::class, 'restore'])->name('tankers.restore')->middleware('can:restore,tanker');
	Route::resource('tankers', TankerController::class)->only([
		'index', 'create', 'store', 'edit', 'update', 'destroy'
	]);

	// Drivers
	Route::put('drivers/{driver}/restore', [DriverController::class, 'restore'])->name('drivers.restore')->middleware('can:restore,driver');
	Route::resource('drivers', DriverController::class)->only([
		'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
	]);

	// Helpers
	Route::put('helpers/{helper}/restore', [HelperController::class, 'restore'])->name('helpers.restore')->middleware('can:restore,helper');
	Route::resource('helpers', HelperController::class)->only([
		'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
	]);

	// // Cashiers
	// Route::put('cashiers/{cashier}/restore', [CashierController::class, 'restore'])->name('cashiers.restore');
	// Route::resource('cashiers', CashierController::class);

	// // Pump Attendants
	// Route::put('pump-attendants/{pumpAttendant}/restore', [PumpAttendantController::class, 'restore'])->name('pump-attendants.restore');
	// Route::resource('pump-attendants', PumpAttendantController::class);

	// // Office Staffs
	// Route::put('office-staffs/{officeStaff}/restore', [OfficeStaffController::class, 'restore'])->name('office-staffs.restore');
	// Route::resource('office-staffs', OfficeStaffController::class);

	// // Station Transactions
	// Route::post('/station-transactions/addNewRow', [StationTransactionController::class, 'addNewRow'])->name('station-transactions.addNewRow');
	// Route::resource('station-transactions', StationTransactionController::class);

	// // Pump Readings
	// Route::resource('pump-readings', PumpReadingController::class)->only(['store', 'destroy']);

	// // Sales
	// Route::resource('sales', SaleController::class)->only(['store', 'destroy']);

	// // Company Vales
	// Route::resource('company-vales', CompanyValeController::class)->only(['store', 'destroy']);

	// // Calibrations
	// Route::resource('calibrations', CalibrationController::class)->only(['store', 'destroy']);

	// // Discounts
	// Route::resource('discounts', DiscountController::class)->only(['store', 'destroy']);
});

require_once __DIR__ . '/jetstream.php';


// Spatie Laravel Backup Package
Route::get('/backup', function () {
    \Illuminate\Support\Facades\Artisan::call('backup:run');
    return 'Successful backup!';
	// Storage::disk('google')->put('hello.txt', 'Hello IBB');
})->name('backup');