<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Flight;

class FlightController extends Controller
{
    public function showFlightForm() {
        $destinations = Destination::all();
        return view('flights.search',compact('destinations'));
    }

    


}
