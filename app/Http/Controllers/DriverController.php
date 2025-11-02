<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Requests\DriverRequest;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverStatusMail;
use Carbon\Carbon;


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

        $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password), 
        'last_name' => $request->last_name,
        'phone_number' => $request->phone_number,
        'country' => $request->country,
        'role' => 'driver', 
    ]);



        $licensePath = MediaServices::save($request->file('license_image'), 'image', 'drivers');

        Driver::create([
            'user_id'          => $user->id,
            'age'              => $request->age,
            'address'          => $request->address,
            'license_image'    => $licensePath,
            'license_category' => $request->license_category,
            'status'           => $request->status,
            'date_of_hire'     => $request->date_of_hire,
            'experience'       => $request->experience,
          
        ]);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully');
    }

    /**
     
     */
    public function show(string $id = null)
    {


       $user = auth()->user();

    if ($user->role === 'driver') {
        $driver = $user->driver;
    } elseif ($user->role === 'admin') {
        if (!$id) abort(400, 'Driver ID required for admin');
        $driver = Driver::findOrFail($id);
    } else {
        abort(403, 'Unauthorized');
    }


        // تحديث الحجوزات الغير مكتملة التي انتهت بالفعل
       $driver->reservations()->where('status', 'pending')->get()->each(function ($reservation) {
            if ($reservation->dropoff_datetime->isPast()) {
            $reservation->update(['status' => 'completed']);
            }
         });


        $reservations = $driver->reservations()
        ->where('status', 'completed')
        ->with('vehicle')
        ->get();

          return view('driver.show', compact('driver', 'reservations'));
    }

    /**
    
     */



     public function pendingBookings()
      {
          $driver = auth()->user()->driver;
          // تحديث الحجوزات الغير مكتملة التي انتهت بالفعل
        $driver->reservations()->where('status', 'pending')->get()->each(function ($reservation) {
        if ($reservation->dropoff_datetime->isPast()) {
            $reservation->update(['status' => 'completed']);
        }
    });

       
         $reservations = $driver->reservations()
            ->where('status', 'pending')
            ->with('vehicle')
            ->get();

    return view('driver.pendingbooking', compact('driver', 'reservations'));
    
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
    $user = $driver->user;

    // ✅ تحديث بيانات المستخدم المرتبط
    $user->update([
        'name'         => $request->name,
        'last_name'    => $request->last_name,
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
        'country'      => $request->country,
    ]);

    // ✅ تحديث صورة الرخصة بطريقة أبسط
    $licensePath = $driver->license_image;

    if ($request->hasFile('license_image')) {
        // حذف الصورة القديمة فقط إن وُجدت
        if ($licensePath && Storage::disk('public')->exists($licensePath)) {
            Storage::disk('public')->delete($licensePath);
        }

        // حفظ الصورة الجديدة
        $licensePath = MediaServices::save($request->file('license_image'), 'image', 'drivers');
    }

    // ✅ تحديث بيانات الـ Driver
    $driver->update([
        'age'              => $request->age,
        'address'          => $request->address,
        'license_image'    => $licensePath,
        'license_category' => $request->license_category,
        'status'           => $request->status,
        'date_of_hire'     => $request->date_of_hire,
        'experience'       => $request->experience,
    ]);

    return redirect()->route('drivers.index')->with('success', 'Driver information updated successfully.');
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





    public function updateStatus(DriverRequest $request, Driver $driver)
{
   

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized');
    }


     $validated = $request->validated();

   
   $updateData = ['status' => $validated['status']];

          if ($validated['status'] === 'approved') {
             $updateData['date_of_hire'] = Carbon::now();
            }

   $driver->update($updateData);

  
    $status = $validated['status'];
    $message = match ($status) {
        'approved' => 'Your account has been approved! You can now log in to the system.',
        'rejected' => 'Sorry, your account has been rejected after review of the information.',
        default => 'Your account status has been changed to under review.',
    };


    Mail::to($driver->user->email)->send(new DriverStatusMail($driver->user->name, $status, $message));

    return redirect()->back()->with('success', 'Driver status updated and email sent successfully.');
  } 




}


