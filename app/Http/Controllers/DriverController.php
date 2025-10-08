<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Requests\DriverRequest;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     
     */
public function index(Request $request)
{
    $query = Driver::query();

    if ($request->filled('search')) {
        $searchTerm = $request->search;

        // إذا البحث A أو B → ابحث بالفئة فقط
        if (in_array(strtoupper($searchTerm), ['A', 'B'])) {
            $query->where('license_category', strtoupper($searchTerm));
        } else {
            // خلاف ذلك → ابحث بالاسم فقط
            $query->where('name', 'like', "%{$searchTerm}%");
        }
    }

    $drivers = $query->get();

    return view('driver.index', compact('drivers'));
}




    /**
     
     */
    public function create()
    {
        return view('driver.create');
    }

    /**
     
     */
    public function store(DriverRequest $request)
    {
        $licensePath = MediaServices::save($request->file('license_image'), 'image', 'drivers');

        Driver::create([
            'name'             => $request->name,
            'age'              => $request->age,
            'address'          => $request->address,
            'license_image'    => $licensePath,
            'license_category' => $request->license_category,
            'status'           => $request->status,
            'date_of_hire'     => $request->date_of_hire,
            'experience'       => $request->experience,
            'email'            => $request->email,
            'phone'            => $request->phone,
        ]);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully');
    }

    /**
     
     */
    public function show(string $id)
    {
        $driver = Driver::findOrFail($id);
        $reservations = $driver->reservations()->with('vehicle')->get();

          return view('driver.show', compact('driver', 'reservations'));
    }

    /**
    
     */
    public function edit(string $id)
    {
        $driver = Driver::findOrFail($id);
        return view('driver.edit', compact('driver'));
    }

    /**
     
     */
    public function update(DriverRequest $request, string $id)
    {
        $driver = Driver::findOrFail($id);

        $licensePath = $driver->license_image;

        if ($request->hasFile('license_image')) {
            $newLicensePath = MediaServices::save($request->file('license_image'), 'image', 'drivers');

            if ($licensePath && Storage::disk('public')->exists($licensePath)) {
                Storage::disk('public')->delete($licensePath);
            }

            $licensePath = $newLicensePath;
        }

        $driver->update(array_merge(
            $request->except(['_token', '_method', 'license_image']),
            ['license_image' => $licensePath]
        ));

        return redirect()->route('drivers.index')->with('success', 'Driver informations updated successfully');
    }

    /**
   
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);

        if ($driver->license_image && Storage::disk('public')->exists($driver->license_image)) {
            Storage::disk('public')->delete($driver->license_image);
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully');
    }
}
