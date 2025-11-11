<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transport;
use App\Http\Requests\TransportRequest;
use App\Services\MediaServices;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transports=Transport::all();
        return view('transport.index',compact('transports'));

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TransportRequest $request)
    {
        //save image
        $imagePath = MediaServices::save($request->file('image'), 'image', 'Transports');

        $transport = Transport::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'image' => $imagePath,
            'type'=>$request->type,
        ]);
        return redirect()->route('transport.index')->with('success','Transport created successfully');

    }

    /**
     * Display the specified resource.
     */
 public function show(string $id)
{
    $transport = Transport::with('vehicles')->findOrFail($id);
    $Vehicles = $transport->vehicles;
    return view('transport.vehicles', compact('transport', 'Vehicles'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransportRequest $request, string $id)
    {
        $transport=Transport::findOrFail($id);
        $transport->update($request->validated());
        return redirect()->route('transport.index')->with('success','Transport updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transport=Transport::findOrFail($id);
        $transport->delete();
        return redirect()->back()->with('success','Transport deleted successfully');
    }
}
