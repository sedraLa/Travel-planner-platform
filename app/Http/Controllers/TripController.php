<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * show the page to choose the type of planning(manual,AI)
     */
    public function view()
    {
        return view('trips.view');
    }

}
