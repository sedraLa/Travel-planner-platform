<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MediaServices;
use App\Models\Transport;
use App\Models\TransportVehicle;
use App\Http\Requests\VehicleRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Driver; // <-- إضافة استدعاء موديل السائق

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicle=TransportVehicle::all();
        return view('vehicles.index',compact('vehicle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $transportId = $request->get('transport_id');
        $drivers = Driver::all(); // <-- جلب جميع السائقين
        return view('vehicles.create', compact('transportId', 'drivers')); // <-- تمريرهم إلى الواجهة
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
            /*'driver_id' => $request->driver_id,*/
            'car_model'=>$request->car_model,
            'plate_number'=>$request->plate_number,
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
         // البحث عن المركبة المراد تعديلها
        $vehicle = TransportVehicle::findOrFail($id);

        // إرجاع عرض (view) يحتوي على نموذج التعديل مع بيانات المركبة
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, string $id)
    {
        // البحث عن المركبة
        $vehicle = TransportVehicle::findOrFail($id);

        // الحصول على مسار الصورة القديمة
        $oldImagePath = $vehicle->image;

        // التحقق مما إذا كان المستخدم قد قام بتحميل صورة جديدة
        if ($request->hasFile('image')) {
            // حفظ الصورة الجديدة
            $imagePath = MediaServices::save($request->file('image'), 'image', 'vehicles');

            // حذف الصورة القديمة إذا كانت موجودة
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        } else {
            // إذا لم يتم تحميل صورة جديدة، احتفظ بالصورة القديمة
            $imagePath = $oldImagePath;
        }

        // تحديث بيانات المركبة
        $vehicle->update([
            'transport_id'   => $request->transport_id,
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

        // إعادة التوجيه إلى صفحة قائمة المركبات مع رسالة نجاح
return redirect()->route('transport.index')->with('success', 'Vehicle updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // البحث عن المركبة
        $vehicle = TransportVehicle::findOrFail($id);

// حذف الصورة المرتبطة بالمركبة من التخزين
        if ($vehicle->image && Storage::disk('public')->exists($vehicle->image)) {
            Storage::disk('public')->delete($vehicle->image);
        }

        // حذف سجل المركبة من قاعدة البيانات
        $vehicle->delete();

        // إعادة التوجيه إلى صفحة القائمة مع رسالة نجاح
        return redirect()->route('transport.index')->with('success', 'Vehicle deleted successfully');
    }
}


