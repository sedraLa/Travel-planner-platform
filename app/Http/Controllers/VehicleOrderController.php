<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleOrderRequest;
use App\Jobs\ProcessNextDriverInChainJob;
use App\Jobs\ProcessReservationDriverMatchingJob;
use App\Models\BookingRequest;
use App\Models\TransportReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GeocodingService;
use App\Services\TransportReservation\ReservationStateManager;

class VehicleOrderController extends Controller
{
    protected ReservationStateManager $stateManager;

    public function __construct(ReservationStateManager $stateManager)
    {
        $this->stateManager = $stateManager;
    }

    public function create(Request $request)
    {
        $message = null;

        if ($request->error === 'no_driver_available') {
            $message = "We couldn't find a driver for you, please try again later.";
        }

        return view('vehicles.order', compact('message'));
    }

    // Send request and create reservation
    public function store(VehicleOrderRequest $request)
    {

        $reservation = TransportReservation::create([
            'user_id' => Auth::id(),
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'pickup_datetime' => $request->pickup_datetime,
            'dropoff_datetime' => $request->pickup_datetime,
            'passengers' => $request->passengers,
            'preferred_category' => $request->category,
            'preferred_type' => $request->type,
            'total_price' => 0,
        ]);

        // set state using stateManager
        $this->stateManager->setInitialState($reservation, 'pending_driver');

        // send job to find a driver
        ProcessReservationDriverMatchingJob::dispatch($reservation->id);

        return redirect()->route('vehicle.searching', $reservation);
    }

    public function searching(TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        // check accessability according to status
        if (!$this->stateManager->canAccessSearching($reservation)) {
            return redirect()->route('vehicle.order', [
                'error' => 'no_driver_available'
            ])->withErrors('Reservation is not in searching state.');
        }

        return view('vehicles.searching', compact('reservation'));
    }

    //polling
    public function status(TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        //check booking requests to decide if we eant to send to the next driver or not
        if ($reservation->status === 'pending_driver') {
            $rankedDriverIds = $reservation->ranked_driver_ids ?? [];

            //get first pending request
            $pendingRequest = BookingRequest::query()
                ->where('reservation_id', $reservation->id)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

                //check if pending time has ended
            if ($pendingRequest && $pendingRequest->expires_at && $pendingRequest->expires_at->isPast()) {
                $pendingRequest->update(['status' => 'expired']);

                //send to next driver
                $currentIndex = array_search($pendingRequest->driver_id, $rankedDriverIds, true);

                ProcessNextDriverInChainJob::dispatchSync(
                    $reservation->id,
                    $rankedDriverIds,
                    $currentIndex === false ? 1 : $currentIndex + 1,
                );

                $reservation->refresh();
            }

            //check if no pending requests left (expired or rejected)
            if (!$pendingRequest) {

                //get last request to decide who is next
                $lastRequest = BookingRequest::query()
                    ->where('reservation_id', $reservation->id)
                    ->latest('id')
                    ->first();


                if ($lastRequest && in_array($lastRequest->status, ['expired', 'rejected'], true)) {
                    $currentIndex = array_search($lastRequest->driver_id, $rankedDriverIds, true); //this driver is done
                    $nextIndex = $currentIndex === false ? 1 : $currentIndex + 1; //move to next driver
                    $nextDriverId = $rankedDriverIds[$nextIndex] ?? null;

                    if ($nextDriverId) {
                        //check if request already sent to next driver (to prevent sending same request to same driver more than once)
                        $alreadySentToNext = BookingRequest::query() 
                            ->where('reservation_id', $reservation->id)
                            ->where('driver_id', $nextDriverId)
                            ->exists();

                        if (!$alreadySentToNext) {
                            ProcessNextDriverInChainJob::dispatchSync($reservation->id, $rankedDriverIds, $nextIndex);
                            $reservation->refresh();
                        }
                    }
                }
            }
        }

        $redirectUrl = null;

        if ($this->stateManager->isDriverAssigned($reservation)) {
            $redirectUrl = route('vehicle.assigned', $reservation);
        }
        if ($this->stateManager->isCancelled($reservation)) {
            $redirectUrl = route('vehicle.order', [
                'error' => 'no_driver_available'
            ]);
        }

        return response()->json([
            'status' => $reservation->status,
            'reservation_id' => $reservation->id,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function assigned(TransportReservation $reservation, GeocodingService $geocoding)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

  
        if (!$this->stateManager->canAccessAssigned($reservation)) {
            return redirect()->route('vehicle.searching', $reservation)
                ->withErrors('Reservation not ready yet.');
        }

        $reservation->load('driver.user', 'vehicle');

        $pickupCoords = $geocoding->geocodeAddress($reservation->pickup_location);
        $dropoffCoords = $geocoding->geocodeAddress($reservation->dropoff_location);

        return view('vehicles.index', [
            'availableVehicles' => collect([$reservation->vehicle]),
            'pickup_location' => $reservation->pickup_location,
            'dropoff_location' => $reservation->dropoff_location,
            'pickup_datetime' => optional($reservation->pickup_datetime)->format('Y-m-d\TH:i'),
            'passengers' => $reservation->passengers,
            'pickupCoords' => $pickupCoords,
            'dropoffCoords' => $dropoffCoords,
            'selectedCategory' => $reservation->vehicle->category,
            'selectedType' => $reservation->vehicle->type,
            'reservation' => $reservation,
        ]);
    }
}
