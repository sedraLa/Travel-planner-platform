<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Driver;
use App\Models\TransportReservation;
use Illuminate\Http\Request;
use App\Http\Requests\DriverRequest;
use App\Services\MediaServices;
use App\Services\DriverApplicationStatusService;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use Carbon\Carbon;


class DriverController extends Controller
{

    /**

     */

    //show the requests from the diver
     public function Requestindex(Request $request)
    {
        $query = Driver::with('user')->where('status', 'pending');

        if ($request->filled('search')) {
            $term = $request->search;

            $query->where(function($q) use ($term) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$term%")
                ->orWhere('last_name', 'like', "%$term%")
                ->orWhere('email', 'like', "%$term%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('license_category')) {
            $query->where('license_category', $request->license_category);
        }

        if ($request->filled('country')) {
            $query->whereHas('user', fn($u) => $u->where('country', 'like', "%{$request->country}%"));
        }

        $query->orderBy('created_at', 'desc');
        
        $drivers = $query->get();

        return view('driver.requestindex', compact('drivers'));
    }

    //show system drivers
    public function Approvedtindex(Request $request)
    {
        $query = Driver::with('user')->where('status', 'Approved');

        if ($request->filled('search')) {
            $term = $request->search;

            $query->where(function($q) use ($term) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$term%")
                ->orWhere('last_name', 'like', "%$term%")
                ->orWhere('email', 'like', "%$term%"));
            });
        }



        if ($request->filled('license_category')) {
            $query->where('license_category', $request->license_category);
        }

        if ($request->filled('country')) {
            $query->whereHas('user', fn($u) => $u->where('country', 'like', "%{$request->country}%"));
        }

        $query->orderBy('date_of_hire', 'desc');

        $drivers = $query->get();

        return view('driver.approvedindex', compact('drivers'));
    }


    //show approved drivers details
    public function show(string $id) {
        $driver = Driver::with('user','assignment.vehicle','reservations')->findOrFail($id);
        $assignment = $driver?->assignment;
        $vehicle = $assignment?->vehicle;
        $pendingBookings = $driver->reservations()->where('status','pending')->count();
        $completedBookings = $driver->reservations()->where('status','completed')->count();
        $canceledBookings = $driver->reservations()->where('status','canceled')->count();
        return view('driver.show', compact([
            'driver',
            'assignment',
            'vehicle',
            'pendingBookings',
            'completedBookings',
            'canceledBookings',
        ]));
    }



 public function ShowDetailsrequest(string $id)
    {


       $driver = Driver::with([ 'user', 'assignment.vehicle', 'assignment.shiftTemplate'])->findOrFail($id);
       
         return view('driver.details', compact('driver'));


    }







//show driver completed bookings for admin and driver
    public function CompletedBookings(string $id = null)
    {
       $user = auth()->user();
    if ($user->role === 'driver') {
        $driver = $user->driver;
    } elseif ($user->role === 'admin') {
        if (!$id) abort(400, 'Driver ID required for admin');
        $driver = Driver::findOrFail($id);
    } else {
        abort(403, 'Unauthorized');
    }
        $reservations = $driver->reservations()
        ->where('driver_status', 'completed')
        ->with(['vehicle', 'user'])
        ->paginate(4); 

          return view('driver.completedbooking', compact('driver', 'reservations'));
    }

   //show driver pending bookings for admin and driver
   public function pendingBookings(string $id = null)
{
       $user = auth()->user();
    if ($user->role === 'driver') {
        $driver = $user->driver;
    } elseif ($user->role === 'admin') {
        if (!$id) abort(400, 'Driver ID required for admin');
        $driver = Driver::findOrFail($id);
    } else {
        abort(403, 'Unauthorized');
    }
        $reservations = $driver->reservations()
        ->where('driver_status', 'pending')
        ->with(['vehicle', 'user'])
        ->where('status', 'confirmed')
        ->whereHas('payment', function ($query) {
            $query->where('status', 'completed');
        })
        ->paginate(4); 
          return view('driver.pendingbooking', compact('driver', 'reservations'));
}


//Delete driver
    public function destroy(string $id)
    {
        $driver = Driver::with('reservations')->findOrFail($id);

        //check driver reservations
        $hasUpcoming = $driver->reservations()
        ->where('pickup_datetime', '>=', now())
        ->exists();

    if ($hasUpcoming) {
        return redirect()->back()->with('error',"You can't delete this Driver because he has upcoming confirmed (paid) reservations");
    }

    //delete license image
        if ($driver->license_image && Storage::disk('public')->exists($driver->license_image)) {
            Storage::disk('public')->delete($driver->license_image);
        }


         if ($guide->personal_image && Storage::disk('public')->exists($guide->personal_image)) {
            Storage::disk('public')->delete($guide->personal_image);
         }
        //delete driver
        $driver->user()->delete();
        $driver->delete();

        return redirect()->route('drivers.approved.index')->with('success', 'Driver deleted successfully');
    }


    //approve or reject driver request
    public function updateStatus(DriverRequest $request, Driver $driver, DriverApplicationStatusService $statusService)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }


        //prevent changing approved drivers
        if ($driver->status === 'approved') {
            return redirect()->back()->with('error', 'Approved drivers status cannot be changed.');
        }

        $validated = $request->validated();

        //enforce choosing (approved,reject)
        if ($validated['status'] === 'pending') {
        return redirect()->back()
            ->with('error', 'Please select a status before confirming.');
    }

        $statusService->updateStatus($driver, $validated['status']);

        if ($validated['status'] === 'rejected') {
            return redirect()->back()->with('success', 'Driver was rejected, email sent, and driver removed.');
        }

        return redirect()->back()->with('success', 'Driver accepted, status updated and email sent successfully.');
    }



//mark reservation as completed
  public function complete($id)
{
    $reservation = TransportReservation::findOrFail($id);
    $now = Carbon::now();
    $pickup = Carbon::parse($reservation->pickup_datetime);

    if ($now->lt($pickup)) {
        return back()->with('error', 'Cannot complete this reservation before pickup time.');
    }

    $isPaidAndConfirmed = $reservation->status === 'confirmed'
    && $reservation->payment()
        ->where('status', 'completed')
        ->exists();

if (!$isPaidAndConfirmed) {
    return back()->with('error', 'Cannot complete this reservation before payment is completed and confirmed.');
}

$driverEarning = round(((float) $reservation->total_price) * 0.20, 2);

$reservation->update([
    'driver_status' => 'completed',
    'driver_earning' => $driverEarning,
]);

    return redirect()->route('driverscompleted.show')->with('success', 'Reservation marked as completed.');
}

//mark reservation as cancelled

public function cancel($id)
{
    $reservation = TransportReservation::findOrFail($id);
    $driver = auth()->user()->driver;

    if ($reservation->driver_id !== $driver->id) {
        abort(403);
    }

    $now = Carbon::now();
    $pickup = Carbon::parse($reservation->pickup_datetime);

    if ($now->lt($pickup)) {
        return back()->with('error', 'Cannot cancel before pickup time.');
    }

    $reservation->update([
        'driver_status' => 'cancelled'
    ]);

    return back()->with('success', 'Reservation marked as cancelled (no-show).');
}


}


