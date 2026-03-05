<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\TransportVehicle;
use App\Models\TransportReservation;




class TransportController extends Controller
{
    public function index() {
        $systemDrivers = Driver::where('status','approved')->count();
        $driverRequests = Driver::where('status','pending')->count();
        $vehicles = TransportVehicle::count();
        $pendingReservations = TransportReservation::where('driver_status','pending')->count();
        $completedReservations = TransportReservation::where('driver_status','completed')->count();
        return view('transport.dashboard',compact(
            'systemDrivers',
            'driverRequests',
            'vehicles',
            'pendingReservations',
            'completedReservations'
        ));
    }
}
