<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\DayActivity;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use App\Models\Activity;
use Carbon\Carbon;

class ManualTripController extends Controller
{

    public function create(Request $request) {
        //store every step data in session
        $data = session('trip_manual',[]);

        // identify which step the user in
        $currentStep = $data['step'] ?? 1;

        //get popular destinations only for step 2
        $popular = Destination::with('images')->orderByDesc('clicks')->take(6)->get();

        //get hotels related to the selected destinations 
        $hotels = [];
        if (isset($data['destination']['destination_id'])) {
            $hotels = Hotel::with('images')
                ->where('destination_id', $data['destination']['destination_id'])
                ->get();
        }

        //calculate user's trip days (for UI)
        $days_count = 0;
        if (
            isset($data['basic']['start_date']) &&
            isset($data['basic']['end_date']) &&
            !empty($data['basic']['start_date']) &&
            !empty($data['basic']['end_date'])
        ) {
            $start = Carbon::parse($data['basic']['start_date']);
            $end = Carbon::parse($data['basic']['end_date']);
            $days_count = $start->diffInDays($end) + 1;
        }

        //get activities related to selected destination
        $activities= [];
        if (isset($data['destination']['destination_id'])) {
            $activities = Activity::where('destination_id',$data['destination']['destination_id'])
            ->get();
        }

        //send data and current step to the view
        return view('trips.manual.create',compact('data','currentStep','popular','hotels','days_count','activities'));
    }

    // get data send from form for each step and deal with it
    public function postStep(Request $request)
    {
        //read step number that sent from form (js)
        $step = (int) $request->input('step', 1);
        //get current session data
        $data = session('trip_manual', []);

        switch ($step) {
            //first step
            case 1:
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string|max:255',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'travelers_number' => 'required|integer|min:1',
                    'budget' => 'nullable|numeric',
                ]);

                //store first step data
                $data['basic'] = $validated;
                //update step
                $data['step'] = 2;
                //update session array
                session(['trip_manual' => $data]);

                return redirect()->route('manual.create');
                break;

            //step 2
            case 2:
                $validated = $request->validate([
                    'destination_id' => 'nullable|exists:destinations,id',
                    'destination_name' => 'nullable|string|max:255',
                ]);

                //error if user didn't enter any destination
                if (empty($validated['destination_id']) && empty($validated['destination_name'])) {
                    return back()->withErrors(['destination' => 'Please select or enter a destination']);
                }

                $data['destination'] = $validated;
                $data['step'] = 3;
                session(['trip_manual' => $data]);

                return redirect()->route('manual.create');
                break;

            //step 3
            case 3:
                $validated = $request->validate([
                    'hotel_name' => 'nullable|array',
                    'hotel_id'=> 'nullable|array'
                ]);

                $data['hotels'] = [
                    'selected' => $validated['hotel_id'] ?? [],
                    'custom' => $validated['hotel_name'] ?? [],
                ];

                $data['step'] = 4;
                session(['trip_manual' => $data]);

                return redirect()->route('manual.create');
                break;

            //step 4
            case 4:
                $validated = $request->validate([
                    'activity_name' => 'nullable|array',
                    'activity_id'=>'nullable|array'
                ]);

                $data['activities'] = [
                    'selected' => $validated['activity_id'] ?? [],
                    'custom' => $validated['activity_name'] ?? [],
                ];

                $data['step'] = 5;
                session(['trip_manual' => $data]);
              

                return redirect()->route('manual.create');
                break;

            //step 5
            case 5:
                $validated = $request->validate([
                    'flight_number' => 'nullable|string|max:20',
                    'airline' => 'nullable|string|max:50',
                    'departure_airport' => 'nullable|string|max:100',
                    'arrival_airport' => 'nullable|string|max:100',
                    'departure_time' => 'nullable|after:now',
                    'arrival_time' => 'nullable|after:departure_time',
                ]);
            
                $data['flight'] = $validated;
            
                
                DB::transaction(function () use ($data) {
                    $trip = Trip::create([
                        'user_id' => auth()->id(),
                        'destination_id' => $data['destination']['destination_id'] ?? null,
                        'destination_name' => $data['destination']['destination_name'] ?? null,
                        'name' => $data['basic']['name'],
                        'description' => $data['basic']['description'] ?? null,
                        'travelers_number' => $data['basic']['travelers_number'],
                        'budget' => $data['basic']['budget'] ?? null,
                        'start_date' => $data['basic']['start_date'],
                        'end_date' => $data['basic']['end_date'],
                        'flight_number' => $data['flight']['flight_number'] ?? null,
                        'airline' => $data['flight']['airline'] ?? null,
                        'departure_airport' => $data['flight']['departure_airport'] ?? null,
                        'arrival_airport' => $data['flight']['arrival_airport'] ?? null,
                        'departure_time' => $data['flight']['departure_time'] ?? null,
                        'arrival_time' => $data['flight']['arrival_time'] ?? null,
                    ]);
            
               //calculate days (for DB)
               $start = Carbon::parse($data['basic']['start_date']);
               $end = Carbon::parse($data['basic']['end_date']);
               $daysCount = $start->diffInDays($end) + 1;

               for ($i=1; $i<=$daysCount; $i++) {
                   $tripDay = TripDay::create([
                       'trip_id' => $trip->id,
                       'day_number' => $i,
                       'hotel_id' => $data['hotels']['selected']["day_$i"] ?? null,
                       'custom_hotel' => $data['hotels']['custom']["day_$i"] ?? null,
                   ]);

                   //save selected activities for these days
                   foreach ($data['activities']['selected']["day_$i"] ?? [] as $activityId) {
                    DayActivity::create([
                        'trip_day_id' => $tripDay->id,
                        'activity_id' => $activityId,
                    ]);
                }

                foreach ($data['activities']['custom']["day_$i"] ?? [] as $customActivity) {
                    if (!empty($customActivity)) {
                        DayActivity::create([
                            'trip_day_id' => $tripDay->id,
                            'custom_activity' => $customActivity,
                        ]);
                    }
                }
            }
        });

           session()->forget('trip_manual');
           return redirect()->route('trip.view')->with('success', 'Trip created successfully!');
           break;
        }
    }

    public function show(Trip $trip)
{
    // Load days and related activities and hotel for the trip
    $trip->load([
        'days.activities.activity',
        'days.hotel'
    ]);

    return view('trips.manual.show', compact('trip'));
}

}
