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
        // أنشئ الحجز بدون تعيين الحالة مباشرة
        $reservation = TransportReservation::create([
            'user_id' => Auth::id(),
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'pickup_datetime' => $request->pickup_datetime,
            'dropoff_datetime' => $request->pickup_datetime,
            'passengers' => $request->passengers,
            'total_price' => 0,
        ]);

        // تعيين الحالة باستخدام StateManager
        $this->stateManager->setInitialState($reservation, 'pending_driver');

        // أرسل Job لمطابقة السائق
        ProcessReservationDriverMatchingJob::dispatch($reservation->id);

        return redirect()->route('vehicle.searching', $reservation);
    }

    public function searching(TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        // تحقق من إمكانية الوصول حسب الحالة
        if (!$this->stateManager->canAccessSearching($reservation)) {
            return redirect()->route('vehicle.order', [
                'error' => 'no_driver_available'
            ])->withErrors('Reservation is not in searching state.');
        }

        return view('vehicles.searching', compact('reservation'));
    }

    public function status(TransportReservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        if ($reservation->status === 'pending_driver') {
            $rankedDriverIds = $reservation->ranked_driver_ids ?? [];

            $pendingRequest = BookingRequest::query()
                ->where('reservation_id', $reservation->id)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($pendingRequest && $pendingRequest->expires_at && $pendingRequest->expires_at->isPast()) {
                $pendingRequest->update(['status' => 'expired']);

                $currentIndex = array_search($pendingRequest->driver_id, $rankedDriverIds, true);

                ProcessNextDriverInChainJob::dispatchSync(
                    $reservation->id,
                    $rankedDriverIds,
                    $currentIndex === false ? 1 : $currentIndex + 1,
                );

                $reservation->refresh();
            }

            if (!$pendingRequest) {
                $lastRequest = BookingRequest::query()
                    ->where('reservation_id', $reservation->id)
                    ->latest('id')
                    ->first();

                if ($lastRequest && in_array($lastRequest->status, ['expired', 'rejected'], true)) {
                    $currentIndex = array_search($lastRequest->driver_id, $rankedDriverIds, true);
                    $nextIndex = $currentIndex === false ? 1 : $currentIndex + 1;
                    $nextDriverId = $rankedDriverIds[$nextIndex] ?? null;

                    if ($nextDriverId) {
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

        // استخدم StateManager لتحديد الانتقال أو الصفحة التالية
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

        // تحقق من الوصول حسب الحالة
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