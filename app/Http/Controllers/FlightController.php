<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Services\AmadeusFlightService;

class FlightController extends Controller
{
    private AmadeusFlightService $flightService;

    public function __construct(AmadeusFlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    public function showFlightForm()
    {
        $destinations = Destination::all();
        return view('flights.search', compact('destinations'));
    }

    //validation form , send request, return rsponse to view
    public function searchFlights(Request $request)
    {
        $validated = $request->validate([
            'country'     => 'required|exists:destinations,id',
            'to-country'  => 'required|exists:destinations,id',
            'departure'   => 'required|date',
            'return'      => 'nullable|date|after_or_equal:departure',
            'seats'       => 'required|integer|min:1',
            'trip-type'   => 'required|in:one-way,round',
        ]);

        if ($validated['trip-type'] === 'round' && empty($validated['return'])) {
            return back()->withErrors([
                'return' => 'Return date is required for round trips.'
            ])->withInput();
        }

        $from = Destination::findOrFail($validated['country']);
        $to   = Destination::findOrFail($validated['to-country']);

        try {
            $outboundFlights = $this->flightService->searchFlights(
                $from,
                $to,
                $validated['departure'],
                $validated['seats']
            );

            $inboundFlights = [];
            if ($validated['trip-type'] === 'round') {
                $inboundFlights = $this->flightService->searchFlights(
                    $to,
                    $from,
                    $validated['return'],
                    $validated['seats']
                );
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('flights.results', [
            'outboundFlights' => $outboundFlights,
            'inboundFlights'  => $inboundFlights,
            'tripType'        => $validated['trip-type'],
        ]);
    }
}