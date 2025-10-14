<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\DayActivity;
use Illuminate\Support\Facades\DB;
use App\Models\Hotel;
use Carbon\Carbon;

class ManualTripController extends Controller
{

    public function create(Request $request) {
        //store every step data in session
        $data = session('trip_manual',[]);

        // identify which step the user in
        $currentStep = $data['step'] ?? 1;

        //get popular destinations only for step 2
        $popular = [];

        $popular = Destination::with('images')->orderByDesc('clicks')->take(10)->get();

        //get hotels related to the selected destinations from the user
        $hotels = [];
        if (isset($data['destination']['destination_id'])) {
            $hotels = Hotel::with('images')
                ->where('destination_id', $data['destination']['destination_id'])
                ->get();
        }

        //calculate user's trip days
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



        //send data and current step to the view
        return view('trips.manual.create',compact('data','currentStep','popular','hotels','days_count'));
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

            //step2
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

            //step 3
            case 3:
                $validated = $request->validate([
                    'hotel_name' => 'nullable|array',
                    'hotel_id'=> 'nullable|array'
                ]);

                $data['hotels'] =[
                    'selected'=>$validated['hotel_id'] ?? [],
                    'custom'=>$validated['hotel_name'] ?? [],
                ];

                $data['step'] = 4;
                session(['trip_manual' => $data]);
                dd(session('trip_manual'));


                return redirect()->route('manual.create');



    }
}

}
