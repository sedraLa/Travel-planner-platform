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
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverStatusMail;
use Carbon\Carbon;


class DriverController extends Controller
{
    /**

     */
    public function index(Request $request)
    {
        $query = Driver::with('user');

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

        $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')");

        $drivers = $query->get();

        return view('driver.index', compact('drivers'));
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
        ->get();

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
        ->get();
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
        //delete driver
        $driver->user()->delete();
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully');
    }


    //approve or reject driver request
    public function updateStatus(DriverRequest $request, Driver $driver)
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


        $updateData = ['status' => $validated['status']];

        //update date of hire if approved
        if ($validated['status'] === 'approved') {
            $updateData['date_of_hire'] = Carbon::now();
        }


        $driver->update($updateData);

        $status = $validated['status'];

        //email messages
        $message = match ($status) {
            'approved' => 'Your account has been approved! You can now log in to the system.',
            'rejected' => 'Sorry, your account has been rejected after review of the information.',
            default => 'Your account status has been changed to under review.',
        };

       Mail::to($driver->user->email)
            ->send(new DriverStatusMail($driver->user->name, $status, $message));

            //delete user if request rejected
        if ($status === 'rejected') {
            if ($driver->license_image && \Storage::disk('public')->exists($driver->license_image)) {
                \Storage::disk('public')->delete($driver->license_image);
            }

            $driver->user()->delete();
            $driver->delete();

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

    $reservation->update(['driver_status' => 'completed']);

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

   

   