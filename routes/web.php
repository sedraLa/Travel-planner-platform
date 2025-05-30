<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\weatherController;


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

Route::get('/', function () {
    return view('auth/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Destination routes
Route::get('/destinations', [DestinationController::class, 'index'])->name('destination.index');
Route::get('/destinations/create', [DestinationController::class, 'create'])->name('destinations.create');
Route::post('/destinations', [DestinationController::class, 'store'])->name('destinations.store');
Route::get('/destinations/{id}', [DestinationController::class, 'show'])->name('destination.show');
Route::get('/destinations/{id}/edit', [DestinationController::class, 'edit'])->name('destinations.edit');
Route::put('/destinations/{id}', [DestinationController::class, 'update'])->name('destinations.update');
Route::delete('/destination-images/{id}', [DestinationController::class, 'destroy'])->name('destination-images.destroy');
Route::post('/destination-images/{id}/set-primary', [DestinationController::class, 'setPrimary'])->name('destination-images.setPrimary');
Route::delete('/destinations/{id}', [DestinationController::class, 'destroyDestination'])->name('destination.destroy');
 // Hotel routes
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::delete('/hotels/{id}', [HotelController::class, 'destroy'])->name('hotels.destroy');
Route::delete('/hotel-images/{id}', [HotelController::class, 'destroyImage'])->name('hotel-images.destroy');

// Weather forecast
Route::get('/weather/{city}', [WeatherController::class, 'show'])->name('weather.forecast');
});

require __DIR__.'/auth.php';
