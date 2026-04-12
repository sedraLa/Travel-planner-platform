<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Enums\UserRole;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */

     public function dashboard() {
        $user = auth()->user(); //get user who is logged now
        if ($user->role === UserRole::DRIVER->value) {
            $driver = $user->driver()?->with('assignment.vehicle', 'assignment.shiftTemplate')->first();
            $vehicle = $driver?->assignment?->vehicle;
            $schedules = $driver?->assignment?->shiftTemplate;
            $pendingBookings = $driver?->reservations()->where('driver_status', 'pending')->count() ?? 0;
            $completedBookings = $driver?->reservations()->where('driver_status', 'completed')->count() ?? 0;
            $canceledBookings = $driver?->reservations()->where('driver_status', 'cancelled')->count() ?? 0;

            return view('driver.dashboard', compact(
                'driver',
                'vehicle',
                'pendingBookings',
                'completedBookings',
                'canceledBookings',
                'schedules'
            ));
        }

        if ($user->role === UserRole::GUIDE->value) {
            $guide = $user->guide()?->with('assignments','guideRequests')->first();
            $trip = $guide?->assignments()
            ->where('status', 'assigned')
            ->whereHas('trip.schedules')
            ->with(['trip.schedules' => function ($q) {
                $q->orderBy('start_date');
            }, 'trip.primaryDestination'])
            ->get()
            ->sortBy(function ($assignment) {
                return optional($assignment->trip->schedules->first())->start_date;
            })
            ->first()?->trip;
            $assignedTrips = $guide?->assignments()->where('status', 'assigned')->count() ?? 0;
            $pendingRequests = $guide?->guideRequests()->where('status','pending')->count() ?? 0;
            $rejectedTrips = $guide?->guideRequests()->where('status','rejected')->count() ?? 0;
            $cancelledTrips = $guide?->reservations()->where('status', 'cancelled')->count() ?? 0;

            return view('guide.dashboard', compact(
                'guide',
                'assignedTrips',
                'pendingRequests',
                'cancelledTrips',
                'rejectedTrips',
                'trip'
            ));
        }

         return view('dashboard');

     }

    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request. (login)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); //authenticate user data

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session. (logout)
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); //logout this user

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
