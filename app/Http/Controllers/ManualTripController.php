<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\DayActivity;
use Illuminate\Support\Facades\DB;

class ManualTripController extends Controller
{

    public function create(Request $request) {
        //get popular destinations (for later)
        /* $popular= Destination::orderBy('clicks','desc')->take(6)->get(); */

        //store every step data in session 
        $data = session('trip_manual',[]); 

        // identify which step the user in
        $currentStep = $data['step'] ?? 1;

        return view('trips.manual.create',compact('data','currentStep'));
 }

 // get data send from form for each step and deal with it 
    public function postStep(Request $request) {
        //define step number
        $step = (int) $request->input('step',1);

        //get current session data
        $data = session('trip_manual',[]);

        //first step
        if($step === 1) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date'=> 'required|date',
                'end_date'=> 'required|date|after_or_equal:start_date',
                'travelers'=>'required|integer|min:1',
                'budget'=> 'nullable|numeric'
            ]);

            //store first step data
            $data['basic'] = $validated;

            //update step
            $data['step'] = 2;

            //update session array
            session(['trip_manual' => $data]);

            //dd(session('trip_manual'));

            

            //redirect user to the same page
            return redirect()->route('manual.create');
        }
    }
}
