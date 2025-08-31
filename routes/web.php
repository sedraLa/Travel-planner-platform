<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\weatherController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\paymentController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\VehicleController;


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


// Weather forecast
Route::get('/weather/{city}', [WeatherController::class, 'show'])->name('weather.forecast');
});

 // Hotel routes
 Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
 Route::get('/hotels/create',[HotelController::class,'create'])->name('hotels.create');
 Route::post('/hotels',[HotelController::class,'store'])->name('hotels.store');
 Route::get('/hotels/{id}',[HotelController::class,'show'])->name('hotel.show');
 Route::get('/hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('hotels.edit');
 Route::put('hotels/{id}',[HotelController::class,'update'])->name('hotels.update');
 Route::delete('hotels/images/{id}',[HotelController::class,'destroyImage'])->name('hotel-images.destroy');
 Route::post('hotels/images/{id}/set-primary', [HotelController::class, 'setPrimaryImage'])->name('hotel-images.setPrimary');
 Route::delete('/hotels/{id}',[HotelController::class,'destroy'])->name('hotels.destroy');

 //reservation routes
 Route::get('/hotels/{id}/reserve', [ReservationController::class, 'showReservationForm'])->name('reservations.form');
 Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
 Route::get('/reservations/{id}/pay', [ReservationController::class, 'pay'])->name('reservations.pay');

// pay routes
Route::post('/payment/paypal/{reservationId}', [PaymentController::class, 'payWithPayPal'])->name('payment.paypal');

// Callback PayPal
Route::get('/payment/paypal/callback', [PaymentController::class, 'paypalCallback'])->name('payment.paypal.callback');

//flight routes
Route::get('/flights/search',[FlightController::class,'showFlightForm'])->name('flight.show');
Route::post('/flights/search', [FlightController::class, 'searchFlights'])->name('flights.search');

//transport routes
Route::get('/transports',[TransportController::class,'index'])->name('transport.index');
Route::post('/transports',[TransportController::class,'store'])->name('transport.store');
Route::put('/transports/{id}',[TransportController::Class,'update'])->name('transport.update');
Route::delete('/transports/{id}',[TransportController::class,'destroy'])->name('transport.destroy');
Route::get('/transports/{id}', [TransportController::class, 'show'])->name('transport.show');


//vehicles routes
Route::get('/vehicles',[VehicleController::class,'index'])->name('vehicle.index');
Route::get('/vehicle/create',[VehicleController::class,'create'])->name('vehicle.create');
Route::post('/vehicle/store',[VehicleController::class,'store'])->name('vehicle.store');
Route::get('vehicel/edit',[VehicleController::class,'edit'])->name('vehicle.edit');


require __DIR__.'/auth.php';
