<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MediaServices;
use App\Models\Transport;
use App\Models\TransportVehicle; 
use App\Http\Requests\VehicleRequest;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $transports= Transport::all();
        return view('vehicles.create',compact('transports'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        //save image
        $imagePath = MediaServices::save($request->file('image'), 'image', 'vehicles');

        $vehicle= TransportVehicle::create([
            'transport_id'   => $request->transport_id,
            'car_model'=>$request->car_model,
            'plate_number'=>$request->plate_number,
            'driver_name'=> $request->driver_name,
            'driver_contact'=>$request->driver_contact,
            'max_passengers' => $request->max_passengers ,
            'base_price'     => $request->base_price ,
            'price_per_km'   => $request->price_per_km,
            'category'       => $request->category ,
            'image'          => $imagePath ,
        ]);

        return redirect()->route('transport.index')->with('success','Vehicle created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
