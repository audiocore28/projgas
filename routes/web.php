<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DriverController;

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

Route::get('/', function () {
    // return view('welcome');
	return Inertia\Inertia::render('Welcome', [
		'foo'	=> 'bar',
	]);
});

Route::get('/about', function () {
	return Inertia\Inertia::render('About', [
		'foo'	=> 'bar',
	]);
});

Route::get('/contact', function () {
	return Inertia\Inertia::render('Contact', [
		'foo'	=> 'bar',
	]);
});


// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return Inertia\Inertia::render('Dashboard');
// })->name('dashboard');

// Drivers

Route::get('drivers', [DriverController::class, 'index'])
    ->name('drivers');

Route::get('/drivers/create', [DriverController::class, 'create'])
    ->name('drivers.create');

Route::post('drivers', [DriverController::class, 'store'])
    ->name('drivers.store');

Route::get('drivers/{contact}/edit', [DriverController::class, 'edit'])
    ->name('drivers.edit');

Route::put('drivers/{contact}', [DriverController::class, 'update'])
    ->name('drivers.update');

Route::delete('drivers/{contact}', [DriverController::class, 'destroy'])
    ->name('drivers.destroy');

Route::put('drivers/{contact}/restore', [DriverController::class, 'restore'])
    ->name('drivers.restore');