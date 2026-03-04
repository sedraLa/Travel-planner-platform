<?php

namespace App\Providers;

use App\Domain\TransportReservation\Ranking\DriverRankingStrategy;
use App\Domain\TransportReservation\Ranking\LastTripAndTripsCountStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(DriverRankingStrategy::class, LastTripAndTripsCountStrategy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
