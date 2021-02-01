<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliveryDetailController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HaulController;
use App\Http\Controllers\HaulDetailController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\TankerLoadController;
use App\Http\Controllers\TankerLoadDetailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseDetailController;
// use App\Http\Controllers\StatementController;
use App\Http\Controllers\SupplierController;
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
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// Purchases
	Route::resource('purchases', PurchaseController::class);

	// Purchase Details
	Route::resource('purchase-details', PurchaseDetailController::class)->only(['store', 'destroy']);

	// Tanker Loads
	Route::resource('tanker-loads', TankerLoadController::class);

	// Tanker Load Details
	Route::resource('tanker-load-details', TankerLoadDetailController::class)->only(['store', 'destroy']);

	// Hauling
	Route::resource('hauls', HaulController::class);

	// Hauling Details
	Route::resource('haul-details', HaulDetailController::class)->only(['store', 'destroy']);

	// Deliveries
	Route::resource('deliveries', DeliveryController::class);

	// Delivery Details
	Route::resource('delivery-details', DeliveryDetailController::class)->only(['store', 'destroy']);

	// Suppliers
	Route::put('suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');
	Route::resource('suppliers', SupplierController::class);

	// Clients
	Route::put('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
	Route::resource('clients', ClientController::class);

	// Statements
	// Route::put('statements/{statement}/restore', [StatementController::class, 'restore'])->name('statements.restore');
	// Route::resource('statements', StatementController::class);

	// Users
	Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore');
	Route::resource('users', UsersController::class);

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
});



// Spatie Laravel Backup Package
Route::get('/backup', function () {
    \Illuminate\Support\Facades\Artisan::call('backup:run');
    return 'Successful backup!';
});