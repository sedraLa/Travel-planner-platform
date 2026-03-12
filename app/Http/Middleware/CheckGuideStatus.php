<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class CheckGuideStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === UserRole::GUIDE->value) {
            $guide = auth()->user()->guide;

            if (!$guide) {
                auth()->logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'No guide profile found.',
                ]);
            }

            switch ($guide->status) {
                case 'pending':
                    auth()->logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your guide account is under review. Please wait for admin approval.',
                    ]);
                case 'rejected':
                    auth()->logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your guide request has been rejected.',
                    ]);
                case 'approved':
                    // allow access
                    break;
                default:
                    auth()->logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your account status is invalid.',
                    ]);
            }
        }

        return $next($request);
    }
}