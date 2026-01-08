<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MediaServices;
use App\Models\Transport;
use App\Models\TransportVehicle;
use App\Http\Requests\VehicleRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Driver; 

class VehicleController extends Controller
{
    
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $transportId = $request->get('transport_id');
        $drivers = Driver::with('user')->where('status','approved')->whereDoesntHave('vehicle') ->get();
        return view('vehicles.create', compact('transportId', 'drivers')); 
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
            'driver_id' => $request->driver_id,
            'car_model'=>$request->car_model,
            'plate_number'=>$request->plate_number,
            'max_passengers' => $request->max_passengers ,
            'base_price'     => $request->base_price ,
            'price_per_km'   => $request->price_per_km,
            'category'       => $request->category ,
            'image'          => $imagePath ,
        ]);

        return redirect()
        ->route('admin.transports.vehicles', $request->transport_id)
        ->with('success','Vehicle created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function vehiclesByTransport($id)
    {
        $transport = Transport::with('vehicles')->findOrFail($id);
        return view('transport.vehicles', [
            'transport' => $transport,
            'Vehicles' => $transport->vehicles
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         
        $vehicle = TransportVehicle::findOrFail($id);
        $drivers = Driver::with('user')->where('status','approved')->whereDoesntHave('vehicle') ->get();
      
        return view('vehicles.edit', compact('vehicle','drivers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, string $id)
    {
        
        $vehicle = TransportVehicle::findOrFail($id);

        
        $oldImagePath = $vehicle->image;

       
        if ($request->hasFile('image')) {
         
            $imagePath = MediaServices::save($request->file('image'), 'image', 'vehicles');

           
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            
            $imagePath = $oldImagePath;
        }

        $vehicle->update([
            'transport_id'   => $request->transport_id,
            'driver_id' => $request->driver_id,
            'car_model'      => $request->car_model,
            'plate_number'   => $request->plate_number,
            'driver_name'    => $request->driver_name,
            'driver_contact' => $request->driver_contact,
            'max_passengers' => $request->max_passengers,
            'base_price'     => $request->base_price,
            'price_per_km'   => $request->price_per_km,
            'category'       => $request->category,
            'image'          => $imagePath,
        ]);

        
        return redirect()
        ->route('admin.transports.vehicles', $vehicle->transport_id)
        ->with('success', 'Vehicle updated successfully');
    
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = TransportVehicle::with('reservations')->findOrFail($id);

        // Check for future confirmed reservations
        $hasUpcoming = $vehicle->reservations()
            ->where('pickup_datetime', '>=', now())
            ->exists();

        if ($hasUpcoming) {
            return back()->withErrors("You can't delete this vehicle because it has upcoming confirmed (paid) reservations");
        }

        // Delete image if exists
        if ($vehicle->image && Storage::disk('public')->exists($vehicle->image)) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        return redirect()
        ->route('admin.transports.vehicles', $vehicle->transport_id)
        ->with('success', 'Vehicle deleted successfully');
    }


}


