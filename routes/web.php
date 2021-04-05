<?php

// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
// use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\BatangasTransactionController;
use App\Http\Controllers\BatangasTransactionDetailController;
use App\Http\Controllers\MindoroTransactionController;
use App\Http\Controllers\MindoroTransactionDetailController;
use App\Http\Controllers\HelperController;
// use App\Http\Controllers\CashierController;
// use App\Http\Controllers\PumpAttendantController;
// use App\Http\Controllers\OfficeStaffController;
use App\Http\Controllers\TankerLoadController;
use App\Http\Controllers\TankerLoadDetailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseDetailController;
// use App\Http\Controllers\StatementController;
use App\Http\Controllers\SupplierController;
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


Route::group(['middleware' => 'auth'], function() {
	// Dashboard
	// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// Purchases
	Route::resource('purchases', PurchaseController::class);

	// Purchase Details
	Route::resource('purchase-details', PurchaseDetailController::class)->only(['store', 'destroy']);

	// Tanker Loads
	Route::resource('tanker-loads', TankerLoadController::class);

	// Tanker Load Details
	Route::resource('tanker-load-details', TankerLoadDetailController::class)->only(['store', 'destroy']);

	// Batangas Transaction
	Route::resource('batangas-transactions', BatangasTransactionController::class);

	// Batangas Transaction Details
	Route::resource('batangas-transaction-details', BatangasTransactionDetailController::class)->only(['store', 'destroy']);

	// Mindoro Transaction
	Route::resource('mindoro-transactions', MindoroTransactionController::class);

	// Mindoro Transaction Details
	Route::resource('mindoro-transaction-details', MindoroTransactionDetailController::class)->only(['store', 'destroy']);

	// Suppliers
	Route::put('suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');
	Route::resource('suppliers', SupplierController::class);

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
	Route::put('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
	Route::resource('clients', ClientController::class);

	// Statements
	// Route::put('statements/{statement}/restore', [StatementController::class, 'restore'])->name('statements.restore');
	// Route::resource('statements', StatementController::class);

	// // Users
	// Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore');
	// Route::resource('users', UsersController::class);

	// Products
	Route::put('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
	Route::resource('products', ProductController::class);

	// Tankers
	Route::put('tankers/{tanker}/restore', [TankerController::class, 'restore'])->name('tankers.restore');
	Route::resource('tankers', TankerController::class);

	// Drivers
	Route::put('drivers/{driver}/restore', [DriverController::class, 'restore'])->name('drivers.restore');
	Route::resource('drivers', DriverController::class);

	// Helpers
	Route::put('helpers/{helper}/restore', [HelperController::class, 'restore'])->name('helpers.restore');
	Route::resource('helpers', HelperController::class);

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



// Spatie Laravel Backup Package
Route::get('/backup', function () {
    \Illuminate\Support\Facades\Artisan::call('backup:run');
    return 'Successful backup!';
	// Storage::disk('google')->put('hello.txt', 'Hello IBB');
})->name('backup');