<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="/css/login.css" rel="stylesheet">
         <!-- CSS تبع مكتبة country-region-selector -->
    <link rel="stylesheet"
    href="https://unpkg.com/country-region-selector@2.1.0/dist/css/crs-country-region-selector.min.css">     <!-- Country -->

    </head>
    <body>
        <div class="background"></div>
        <div class="login-container">
            <div class="form-login">
                <div class="form-background"></div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>





