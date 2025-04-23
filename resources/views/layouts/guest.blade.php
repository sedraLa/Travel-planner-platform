<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="/css/login.css" rel="stylesheet">

    </head>
    <body>
        <div class="background"></div>
        <div class="login-container">
            <div class="left">
                <h2>Travel &</h2>
                <h1>Explore Horizons</h1>
                <p>
                    Where your dream destinations become reality.
                    Embark on a journey where every corner of the world is within your reach.
                </p>
            </div>

            <div class="form-login">
                <div class="form-background"></div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
