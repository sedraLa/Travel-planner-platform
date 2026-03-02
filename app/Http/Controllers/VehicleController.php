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
        // جلب الـ drivers المصرح لهم واللي ما عندهم سيارة
       

        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        
        

        // حفظ الصورة
        $imagePath = $request->hasFile('image')
            ? MediaServices::save($request->file('image'), 'image', 'vehicles')
            : null;

        $vehicle = TransportVehicle::create([
           
            'car_model'      => $request->car_model,
            'plate_number'   => $request->plate_number,
            'max_passengers' => $request->max_passengers,
            'base_price'     => $request->base_price,
            'price_per_km'   => $request->price_per_km,
            'category'       => $request->category,
            'type'           => $request->type,
            'image'          => $imagePath,
            
        ]);

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Vehicle created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function Index(Request $request)
    {
        $query = TransportVehicle::query();
        


           if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('car_model', 'like', '%' . $searchTerm . '%')
                    ->orWhere('plate_number', 'like', '%' . $searchTerm . '%');
                    
            });
        }


          if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

         if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('max_passengers')) {
            $query->where('max_passengers', $request->max_passengers);
        
        }


        if ($request->filled('base_price')) {
            $query->where('base_price', $request->base_price);
        
        }


      if ($request->filled('driver_id')) {
      $query->where('driver_id', $request->driver_id);
      }


        $vehicles=$query->get();
        return view('transport.vehicles', compact('vehicles'));
           
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = TransportVehicle::findOrFail($id);

        
    

        return view('vehicles.edit', compact('vehicle'));
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

        
        // check driver is not assigned to another vehicle
      

        $vehicle->update([
            
            'car_model'      => $request->car_model,
            'plate_number'   => $request->plate_number,
            'driver_name'    => $request->driver_name,
            'driver_contact' => $request->driver_contact,
            'max_passengers' => $request->max_passengers,
            'base_price'     => $request->base_price,
            'price_per_km'   => $request->price_per_km,
            'category'       => $request->category,
            'image'          => $imagePath,
            'type'           => $request->type,
        ]);

        return redirect()
            ->route('admin.vehicles.index' )
            ->with('success', 'Vehicle updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = TransportVehicle::with('reservations')->findOrFail($id);

        // تحقق من وجود حجوزات مستقبلية مؤكدة
        $hasUpcoming = $vehicle->reservations()
            ->where('pickup_datetime', '>=', now())
            ->exists();

        if ($hasUpcoming) {
            return back()->withErrors("You can't delete this vehicle because it has upcoming confirmed (paid) reservations");
        }

        // حذف الصورة إذا موجودة
        if ($vehicle->image && Storage::disk('public')->exists($vehicle->image)) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully');
    }
}
