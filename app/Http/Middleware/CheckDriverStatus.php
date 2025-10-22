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

        // لو ما في صف driver أو الحالة مش approved
        if (! $driver || $driver->status !== 'approved') {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'حسابك تحت المراجعة أو مرفوض. سيتم إبلاغك بعد الموافقة.',
            ]);
        }
    }
        return $next($request);
    }
}
