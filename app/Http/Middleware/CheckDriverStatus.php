<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class CheckDriverStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if (auth()->check() && auth()->user()->role === UserRole::DRIVER->value) {
        $driver = auth()->user()->driver;



        if (! $driver) {
                auth()->logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'No driver profile found.',
                ]);
            }



       switch ($driver->status) {
                case 'pending':
                    auth()->logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your account is under review. Please check your email.',
                    ]);
                case 'rejected':
                    auth()->logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your account request has been rejected.',
                    ]);
                case 'approved':
                    // allow login
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
