<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

   
        $this->renderable(function (AuthorizationException $e, $request) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You are not allowed to perform this action.'
                ], 403);
            }

            return redirect()->back()->with(
                'error',
                'You cannot review this item (either already reviewed or not eligible).'
            );
        });
    }
}