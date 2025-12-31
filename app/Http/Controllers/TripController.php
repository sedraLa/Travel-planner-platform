<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Trip;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = Trip::where('user_id', Auth::id())
        ->latest()
        ->get();
        return view('trips.index',compact('trips'));
    }

    /**
     * show the page to choose the type of planning(manual,AI)
     */
    public function view()
    {
        return view('trips.view');
    }

}
