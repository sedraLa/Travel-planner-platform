<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialization;
use App\Http\Requests\SpecializationRequest;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
          $specializations = Specialization::all();
          return view('specializations.index', compact('specializations'));
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecializationRequest $request)
    {
         Specialization::create($request->validated());

        return redirect()
            ->route('specialization.index')
            ->with('success','Specialization created');
    }

  

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         
          $specialization= Specialization::findOrFail($id);

          if ($specialization->guides()->count() > 0) {
           return redirect()
            ->route('specialization.index')
            ->with('error', 'Cannot delete specialization because there are guides linked to it.');
    }
          $specialization->delete();
         return redirect()
            ->route('specialization.index')
            ->with('success','Specialization deleted');
    }
}
