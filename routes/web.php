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
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\VehicleOrderController;
use App\Http\Controllers\TransportReservationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\AiTripController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ManualTripController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AiTestController;


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

// صفحة اختيار نوع التسجيل
Route::get('/register/select-role', function () {
    return view('auth.selectRole');
})->name('register.select-role');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'check.driver.status'])->name('dashboard');


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
Route::get('/reservations', [ReservationController::class, 'index']) ->name('reservations.index');

// pay routes
Route::post('/payment/paypal/{reservationId}', [PaymentController::class, 'payWithPayPal'])->name('payment.paypal');


// Callback PayPal
Route::get('/payment/paypal/callback', [PaymentController::class, 'paypalCallback'])->name('payment.paypal.callback');
Route::get('/payment/paypal/transport/callback', [PaymentController::class, 'paypalCallbackTransport'])
    ->name('payment.transport.callback');

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
// USER – browse vehicles
Route::get('/vehicles', [VehicleController::class, 'index'])
    ->name('vehicles.index');

// ADMIN – manage vehicles
Route::get('/admin/transports/{transport}/vehicles',
    [VehicleController::class, 'vehiclesByTransport']
)->name('admin.transports.vehicles');


Route::get('/admin/vehicles/create', [VehicleController::class, 'create'])
    ->name('admin.vehicles.create');

Route::post('/admin/vehicles', [VehicleController::class, 'store'])
    ->name('admin.vehicles.store');

Route::get('/admin/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])
    ->name('admin.vehicles.edit');

Route::put('/admin/vehicles/{vehicle}', [VehicleController::class, 'update'])
    ->name('admin.vehicles.update');

Route::delete('/admin/vehicles/{vehicle}', [VehicleController::class, 'destroy'])
    ->name('admin.vehicles.destroy');

//order vehicles
Route::get('/vehicle/order/{id}',[VehicleOrderController::class, 'create'])->name('vehicle.order');
Route::post('/transports/{id}/available', [VehicleOrderController::class, 'store'])
    ->name('vehicle.search');

Route::get('/vehicle/reservation/{id}',[TransportReservationController::class,'create'])->name('vehicle.reservation');

Route::post('/transports/{transportId}/vehicles/{vehicleId}/reservation',
    [TransportReservationController::class, 'store']
)->name('vehicleReservation.store');


    Route::get('vehicles/paypal/{reservation}', [PaymentController::class, 'payWithPayPalTransport'])
    ->name('vehicles.paypal');








 //Drivers Routes
Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
Route::get('/driver/create', [DriverController::class, 'create'])->name('drivers.create');
Route::post('/driver/store', [DriverController::class, 'store'])->name('drivers.store');
// للسائق (بدون ID)
Route::middleware(['auth']) ->prefix('driver') ->group(function () {

 Route::get('/show', [DriverController::class, 'show'])->name('driverscompleted.show');

 Route::get('/bookings/pending', [DriverController::class, 'pendingBookings'])->name('bookings.pending');

    });


// للأدمن (مع ID)

Route::get('/driver/{id}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
Route::put('/driver/{id}/update', [DriverController::class, 'update'])->name('drivers.update');
Route::delete('/driver/{id}/destroy', [DriverController::class, 'destroy'])->name('drivers.destroy');
Route::patch('/drivers/{driver}/status', [DriverController::class, 'updateStatus'])->name('drivers.updateStatus');
Route::post('/reservations/{id}/complete',[DriverController::class, 'complete'])->name('reservations.complete');
Route::post('/reservations/{id}/cancel',[DriverController::class,'cancel'])->name('reservation.cancel');
Route::middleware(['auth']) ->prefix('admin') ->group(function () {
   Route::get('/driver/{id}/show', [DriverController::class, 'show'])->name('drivers.show');
   Route::get('/drivers/{id}/pending-bookings', [DriverController::class, 'pendingBookings'])->name('admin.bookings.pending');
 });

//favoritefeture routes
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favorites/{type}/{id}', [FavoriteController::class, 'store'])
    ->name('favorites.add')
    ->middleware('auth');
    // Route to show the user's favorites page
Route::get('/show-favourite', [FavoriteController::class, 'showFavorites'])->name('favorites.show')->middleware('auth');
//Trip routes
Route::get('/trip/view',[TripController::class,'view'])->name('trip.view');
Route::get('/trips/manual/create',[ManualTripController::class,'create'])->name('manual.create'); //show form for creating manual trip
Route::post('/trips/manual/step',[ManualTripController::class,'postStep'])->name('manual.step'); //handle step submits
Route::post('/trips/manual/finish',[ManualTripController::class,'finish'])->name('manual.finish'); //finalize (later)
Route::get('manual/{trip}',[ManualTripController::class,'show'])->name('manual.show');
Route::get('/trips',[TripController::class,'index'])->name('trips.index');
Route::delete('/trips/{trip}',[ TripController::class,'destroy'])->name('trip.destroy');




//AI trip routes
Route::get('/trips/ai/create',[AiTripController::class,'create'])->name('ai.create');
Route::post('/trips/ai/generate',[AiTripController::class,'generate'])->name('ai.generate');
Route::get('/trips/{trip}', [AiTripController::class, 'show'])->name('ai.show');





//Activities routes
Route::get('/activities',[ActivityController::class,'index'])->name('activities.index');
Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');
Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');


Route::get('/transport-reservations', [TransportReservationController::class, 'index'])
    ->name('transport.reservations.index');

//notifications routes
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');




require __DIR__.'/auth.php';





