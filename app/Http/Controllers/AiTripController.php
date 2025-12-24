<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AiTripController extends Controller
{
    public function create () {
        return view('trips.ai.create');
    }
}
