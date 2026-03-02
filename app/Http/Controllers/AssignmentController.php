<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentRequest;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use App\Models\TransportVehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class AssignmentController extends Controller
{

   public function index()
  {

        $assignments = Assignment::with(['vehicle', 'driver.user', 'shiftTemplate'])
        ->latest() ;


        if ($request->filled('search')) {
        $search = $request->search;
        $assignments = $assignments->where(function ($q) use ($search) {
            $q->whereHas('vehicle', function ($qv) use ($search) {
                $qv->where('car_model', 'like', "%{$search}%")
                   ->orWhere('plate_number', 'like', "%{$search}%");
            })
            ->orWhereHas('driver.user', function ($qd) use ($search) {
                $qd->whereRaw("CONCAT(name, ' ', COALESCE(last_name, '')) like ?", ["%{$search}%"]);
            });
        });
    }

    $assignments = $assignments->get();

    // جلب السيارات والسائقين اللي عندهم إسناد فقط
    $vehicles = $assignments->pluck('vehicle')->unique('id')->values();
    $drivers = $assignments->pluck('driver')->unique('id')->values();

    return view('assignments.index', compact('assignments', 'vehicles', 'drivers'));


}


  public function create()
{
  
    $vehicles = TransportVehicle::orderBy('car_model')->get();
    $shiftTemplates = ShiftTemplate::orderBy('name')->get();
    $drivers = Driver::where('status', 'approved')
    ->doesntHave('assignment')
    ->orderBy('id')
    ->get();

    return view('assignments.create', compact(   'vehicles', 'drivers', 'shiftTemplates'));
}

    public function store(AssignmentRequest $request)
    {
        $validated = $request->validated();

        $shiftTemplate = ShiftTemplate::findOrFail($validated['shift_template_id']);

        $vehicleConflict = Assignment::with('shiftTemplate')
            ->where('transport_vehicle_id', $validated['transport_vehicle_id'])
            ->get()
            ->first(fn (Assignment $assignment) => $this->hasShiftConflict($assignment->shiftTemplate, $shiftTemplate));

        if ($vehicleConflict) {
            return back()->withErrors([
                'shift_template_id' => 'This vehicle already has an overlapping shift assignment.',
            ]);
        }

       $driver = Driver::findOrFail($validated['driver_id']);

       if ($driver->assignment_id !== null) {
         return back()->withErrors([
        'driver_id' => 'This driver already has an assignment.',
       ]);
          }

        $assignment = Assignment::create([
            'transport_vehicle_id' => $validated['transport_vehicle_id'],
            'shift_template_id' => $validated['shift_template_id'],
        ]);

        $driver->update([
            'assignment_id' => $assignment->id,
        ]);

        return back()->with('success', 'Assignment created successfully.');
    }





    public function edit(Assignment $assignment)
   {
  
    $vehicles = TransportVehicle::orderBy('car_model')->get();
    $shiftTemplates = ShiftTemplate::orderBy('name')->get();
    $drivers = Driver::where('status', 'approved')
        ->where(function ($q) use ($assignment) {
            $q->doesntHave('assignment')
              ->orWhere('id', $assignment->driver_id); // السائق الحالي يظل ضمن القائمة
        })
        ->orderBy('id')
        ->get();

    return view('assignments.edit', compact('assignment', 'vehicles', 'drivers', 'shiftTemplates'));
   }

    



   public function update(AssignmentRequest $request, Assignment $assignment)
  {
    $validated = $request->validated();

    $shiftTemplate = ShiftTemplate::findOrFail($validated['shift_template_id']);

    
    $vehicleConflict = Assignment::with('shiftTemplate')
        ->where('transport_vehicle_id', $validated['transport_vehicle_id'])
        ->where('id', '!=', $assignment->id) // تجاهل الـ assignment الحالي
        ->get()
        ->first(fn (Assignment $assignment) => $this->hasShiftConflict($assignment->shiftTemplate, $shiftTemplate));
    if ($vehicleConflict) {
        return back()->withErrors([
            'shift_template_id' => 'This vehicle already has an overlapping shift assignment.',
        ]);
    }

    $driver = Driver::findOrFail($validated['driver_id']);
    

    // التحقق من أن السائق لا يملك assignment آخر غير هذا
    if ($driver->assignment_id && $driver->assignment_id != $assignment->id) {
        return back()->withErrors([
            'driver_id' => 'This driver already has an assignment.',
        ]);
    }


     Driver::where('assignment_id', $assignment->id)
        ->where('id', '!=', $driver->id)
        ->update(['assignment_id' => null]);


     $driver->update([
        'assignment_id' => $assignment->id,
    ]);



    $assignment->update([
        'transport_vehicle_id' => $validated['transport_vehicle_id'],
        'shift_template_id' => $validated['shift_template_id'],
    ]);

   
    

   
    return back()->with('success', 'Assignment updated successfully.');
   }



 
    public function destroy(Assignment $assignment)
    {
        Driver::where('assignment_id', $assignment->id)->update(['assignment_id' => null]);
        $assignment->delete();

        return back()->with('success', 'Assignment deleted successfully.');
    }

 

    private function hasShiftConflict(?ShiftTemplate $firstShift, ?ShiftTemplate $secondShift): bool
    {
        if (! $firstShift || ! $secondShift) {
            return false;
        }

        $firstDays = $firstShift->days_of_week ?? [];
        $secondDays = $secondShift->days_of_week ?? [];

        if (empty(array_intersect($firstDays, $secondDays))) {
            return false;
        }

        $firstStart = Carbon::createFromFormat('H:i:s', $firstShift->start_time);
        $firstEnd = Carbon::createFromFormat('H:i:s', $firstShift->end_time);
        $secondStart = Carbon::createFromFormat('H:i:s', $secondShift->start_time);
        $secondEnd = Carbon::createFromFormat('H:i:s', $secondShift->end_time);

        return $firstStart->lt($secondEnd) && $secondStart->lt($firstEnd);
    }
}