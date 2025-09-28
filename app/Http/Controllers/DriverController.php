<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Http\Requests\DriverRequest;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    /**
     * عرض قائمة بجميع السائقين
     */
    public function index()
    {
        $drivers = Driver::all();
        return view('driver.index', compact('drivers'));
    }

    /**
     * عرض نموذج إنشاء سائق جديد
     */
    public function create()
    {
        return view('driver.create');
    }

    /**
     * تخزين سائق جديد في قاعدة البيانات
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

        return redirect()->route('drivers.index')->with('success', 'تم إنشاء السائق بنجاح.');
    }

    /**
     * عرض تفاصيل سائق محدد
     */
    public function show(string $id)
    {
        $driver = Driver::findOrFail($id);
        return view('driver.show', compact('driver'));
    }

    /**
     * عرض نموذج تعديل بيانات السائق
     */
    public function edit(string $id)
    {
        $driver = Driver::findOrFail($id);
        return view('driver.edit', compact('driver'));
    }

    /**
     * تحديث بيانات السائق في قاعدة البيانات
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

        return redirect()->route('drivers.index')->with('success', 'تم تحديث بيانات السائق بنجاح.');
    }

    /**
     * حذف سائق من قاعدة البيانات
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);

        if ($driver->license_image && Storage::disk('public')->exists($driver->license_image)) {
            Storage::disk('public')->delete($driver->license_image);
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'تم حذف السائق بنجاح.');
    }
}
