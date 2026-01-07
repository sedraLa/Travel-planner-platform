<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Check if user is logged in
     */
    protected function redirectTo(Request $request): ?string //where the user should be redirected if is not logged in
    {
        return $request->expectsJson() ? null : route('login');
    }
}

