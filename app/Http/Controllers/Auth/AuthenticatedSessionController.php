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
        if ($user->role !==UserRole::DRIVER->value) {
            return view('dashboard');
        }

        $driver = $user->driver;
        $vehicle = $driver?->vehicle ;//null safe operator(return null if not exist)
        $pendingBookings = $driver?->reservations()->where('driver_status', 'pending')->count() ?? 0;
        $completedBookings = $driver?->reservations()->where('driver_status','completed')->count() ?? 0;
        $canceledBookings = $driver?->reservations()->where('driver_status','cancelled')->count() ?? 0;
        //$earnings = $driver?->reservations()->where('driver_status', 'completed')->sum('total_price') ?? 0;
        return view('driver.dashboard',compact(
            'driver',
            'vehicle',
            'pendingBookings',
            'completedBookings',
            'canceledBookings'
        ));

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
