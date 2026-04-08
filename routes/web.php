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
use App\Http\Controllers\AiTripCompletionController;
use App\Http\Controllers\AiTripGenerationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ManualTripController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AiTestController;
use App\Http\Controllers\ShiftTemplateController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Driver\BookingRequestController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\AdminGuideController;
use App\Http\Controllers\AdminGuideApplicationController;



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


Route::get('/register/select-role', function () {
    return view('auth.selectRole');
})->name('register.select-role');

Route::get('/dashboard',[AuthenticatedSessionController::class, 'dashboard'])
->middleware(['auth', 'verified', 'check.driver.status','check.guide.status'])
->name('dashboard');


//Admin Routes

    // Admin Destinations
Route::middleware('check.admin')->group(function () {
Route::get('/destinations/create', [DestinationController::class, 'create'])->name('destinations.create');
Route::post('/destinations', [DestinationController::class, 'store'])->name('destinations.store');
Route::get('/destinations/{id}/edit', [DestinationController::class, 'edit'])->name('destinations.edit');
Route::put('/destinations/{id}', [DestinationController::class, 'update'])->name('destinations.update');
Route::delete('/destination-images/{id}', [DestinationController::class, 'destroy'])->name('destination-images.destroy');
Route::post('/destination-images/{id}/set-primary', [DestinationController::class, 'setPrimary'])->name('destination-images.setPrimary');
Route::delete('/destinations/{id}', [DestinationController::class, 'destroyDestination'])->name('destination.destroy');

//Admin Hotels
Route::get('/hotels/create',[HotelController::class,'create'])->name('hotels.create');
Route::post('/hotels',[HotelController::class,'store'])->name('hotels.store');
Route::get('/hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('hotels.edit');
Route::put('hotels/{id}',[HotelController::class,'update'])->name('hotels.update');
Route::delete('hotels/images/{id}',[HotelController::class,'destroyImage'])->name('hotel-images.destroy');
Route::post('hotels/images/{id}/set-primary', [HotelController::class, 'setPrimaryImage'])->name('hotel-images.setPrimary');
Route::delete('/hotels/{id}',[HotelController::class,'destroy'])->name('hotels.destroy');



// ADMIN Vehicles
Route::get('/admin/vehicles',
    [VehicleController::class, 'Index']
)->name('admin.vehicles.index');
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

 //Admin Drivers 
 //Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
Route::get('/drivers/request', [DriverController::class, 'Requestindex'])->name('drivers.request.index');
Route::get('/drivers/approved', [DriverController::class, 'Approvedtindex'])->name('drivers.approved.index');
Route::get('/drivers/{id}/details/request', [DriverController::class, 'ShowDetailsrequest'])->name('drivers.show.details.requset');
 Route::delete('/driver/{id}/destroy', [DriverController::class, 'destroy'])->name('drivers.destroy');
Route::patch('/drivers/{driver}/status', [DriverController::class, 'updateStatus'])->name('drivers.updateStatus');
Route::get('/driver/{id}/completed-bookings', [DriverController::class, 'CompletedBookings'])->name('admin.bookings.completed');
Route::get('/drivers/{id}/pending-bookings', [DriverController::class, 'pendingBookings'])->name('admin.bookings.pending');
Route::get('/drivers/details/{id}',[DriverController::class, 'show'])->name('drivers.details');


//Admin Shift Templates
Route::get('/admin/shift-templates', [ShiftTemplateController::class,'index'])->name('shift-templates.index');
Route::post('/shift-templates', [ShiftTemplateController::class, 'store'])->name('shift-templates.store');
Route::delete('/shift-templates/{id}', [ShiftTemplateController::class, 'destroy'])->name('shift-templates.destroy');

//Admin Assignment
Route::get('/admin/assignments',[AssignmentController::class,'index'])->name('assignments.index');
Route::get('/admin/assignments/create',[AssignmentController::class,'create'])->name('assignments.create');
Route::post('/admin/assignments/store', [AssignmentController::class,'store'])->name('assignments.store');
Route::get('/admin/assignments/edit/{assignment}', [AssignmentController::class,'edit'])->name('assignments.edit');
Route::put('/admin/assignments/update/{assignment}', [AssignmentController::class,'update'])->name('assignments.update');
Route::delete('/admin/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

//Admin transport dashboard
Route::get('/admin/transport/dashboard',[TransportController::class,'index'])->name('transport.dashboard');


//Admin Activities
Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');
Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

//trips dashboard
Route::get('/admin/trips/dashboard',[TripController::class,'dashboard'])->name('trips.dashboard');
//Admin Specialization

Route::get('/admin/specialization',[SpecializationController::class,'index'])->name('specialization.index');
Route::post('/admin/specialization/store',[SpecializationController::class,'store'])->name('specialization.store');
Route::delete('/admin/specialization/{id}',[SpecializationController::class,'destroy'])->name('specialization.destroy');

//Admin Guide Applications
Route::get('/admin/guide/applications',[AdminGuideApplicationController::class,'index'])->name('guide-applications.index');
Route::get('/admin/guide/application/{id}',[AdminGuideApplicationController::class,'show'])->name('guide-applications.show');
Route::patch('/admin/guides/{guide}/status',[AdminGuideApplicationController::class,'updateStatus'])->name('guide.updateStatus');

//Admin Guide
 
Route::get('/guide/index', [AdminGuideController::class, 'index'])->name('guides.index');
Route::delete('/guide/{id}/destroy', [AdminGuideController::class, 'destroy'])->name('guides.destroy');
//Route::get('/driver/{id}/completed-bookings', [DriverController::class, 'CompletedBookings'])->name('admin.bookings.completed');
//Route::get('/drivers/{id}/pending-bookings', [DriverController::class, 'pendingBookings'])->name('admin.bookings.pending');
Route::get('/guides/details/{id}',[AdminGuideController::class,'show'])->name('guides.details');


    });

    
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Destination routes
Route::get('/destinations', [DestinationController::class, 'index'])->name('destination.index');
Route::get('/destinations/{id}', [DestinationController::class, 'show'])->name('destination.show');

// Weather forecast
Route::get('/weather/{city}', [WeatherController::class, 'show'])->name('weather.forecast');

 // Hotel routes
 Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
 Route::get('/hotels/{id}',[HotelController::class,'show'])->name('hotel.show');

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



//transport reservations
Route::get('/vehicle-reservations',  [TransportReservationController::class, 'index'])->name('vehicle.reservations.index');

//vehicles routes
// USER – browse vehicles

//انتبهيييRoute::get('/vehicles', [VehicleController::class, 'index'])
    //->name('vehicles.index');

//order vehicles
Route::get('/vehicle/order',[VehicleOrderController::class, 'create'])->name('vehicle.order');
Route::post('/vehicles/search',  [VehicleOrderController::class, 'store'])->name('vehicle.search');
Route::get('/vehicle/searching/{reservation}',[VehicleOrderController::class, 'searching'])->name('vehicle.searching');
Route::get('/vehicle/searching/{reservation}/status',[VehicleOrderController::class, 'status'])->name('vehicle.searching.status');
Route::get('/vehicle/assigned/{reservation}', [VehicleOrderController::class, 'assigned'])->name('vehicle.assigned');

Route::get('/vehicle/reservation/{reservation}',[TransportReservationController::class,'create'])->name('vehicle.reservation');

Route::post('/vehicles/{reservation}/reservation',[TransportReservationController::class, 'store'])
->name('vehicleReservation.store');

Route::get('vehicles/paypal', [PaymentController::class, 'payWithPayPalTransport'])
    ->name('vehicles.paypal');


//favoritefeture routes
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favorites/{type}/{id}', [FavoriteController::class, 'store'])
    ->name('favorites.add')
    ->middleware('auth');
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
Route::post('/trips/ai/generate',[AiTripGenerationController::class,'generate'])->name('ai.generate');
Route::get('/trips/{trip}', [AiTripController::class, 'show'])->name('ai.show');
Route::get('/trips/{trip}/complete', [AiTripController::class, 'editCompletion'])->name('trip.complete.edit');
Route::post('/trips/{trip}/complete/basics', [AiTripCompletionController::class, 'saveBasics'])->name('trip.complete.basics');
Route::post('/trips/{trip}/complete/days', [AiTripCompletionController::class, 'saveDaysActivities'])->name('trip.complete.days');
Route::post('/trips/{trip}/complete/packages', [AiTripCompletionController::class, 'savePackages'])->name('trip.complete.packages');
Route::post('/trips/{trip}/complete/schedules', [AiTripCompletionController::class, 'saveSchedules'])->name('trip.complete.schedules');
Route::post('/trips/{trip}/complete/images', [AiTripCompletionController::class, 'saveImages'])->name('trip.complete.images');

//Activities routes
Route::get('/activities',[ActivityController::class,'index'])->name('activities.index');
Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');


//notifications routes
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');

});



//Driver routes
Route::middleware(['auth','check.driver.status']) ->prefix('driver') ->group(function () {

 Route::get('/show', [DriverController::class, 'CompletedBookings'])->name('driverscompleted.show');

 Route::get('/bookings/pending', [DriverController::class, 'pendingBookings'])->name('bookings.pending');
 Route::post('/reservations/{id}/complete',[DriverController::class, 'complete'])->name('reservations.complete');

 Route::post('/reservations/{id}/cancel',[DriverController::class,'cancel'])->name('reservation.cancel');

 Route::get('/booking-requests', [BookingRequestController::class, 'index'])->name('driver.booking-requests.index');
 Route::post('/booking-requests/{bookingRequest}/accept', [BookingRequestController::class, 'accept'])->name('driver.booking-requests.accept');
 Route::post('/booking-requests/{bookingRequest}/reject', [BookingRequestController::class, 'reject'])->name('driver.booking-requests.reject');
 Route::get('/pending-reservations', [BookingRequestController::class, 'pendingReservations'])->name('driver.pending-reservations');


    });




require __DIR__.'/auth.php';





