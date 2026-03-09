<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShiftTemplateRequest;
use App\Models\ShiftTemplate;
use Illuminate\Http\Request;

class ShiftTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = ShiftTemplate::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $shiftTemplates = $query->latest()->paginate(10)->withQueryString();

        return view('shifts.shifttemplate', compact('shiftTemplates'));
    }


    

    public function store(ShiftTemplateRequest $request)
    {
        $validated = $request->validated();
    
       
        $daysSorted = collect($validated['days_of_week'])->sort()->values()->toArray();
    
    
        $exists = ShiftTemplate::sameTimeAndDays(
            $validated['start_time'],
            $validated['end_time'],
            $daysSorted
        )->isNotEmpty();
    
        if ($exists) {
            return back()->withErrors([
                'start_time' => 'Shift with same time and days already exists.'
            ])->withInput();
        }
    
      
        $validated['days_of_week'] = $daysSorted;
        ShiftTemplate::create($validated);
    
        return redirect()->route('shift-templates.index')
            ->with('success', 'Shift template created successfully.');
    }


     public function destroy(string $id)
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        
        $shiftTemplate = ShiftTemplate::findOrFail($id);

        if ($shiftTemplate->assignments()->exists()) {
        return redirect()->route('shift-templates.index')
       ->with('error', 'Cannot delete this shift template because it has assignments.');
    }

        $shiftTemplate->delete();
        

        
        return redirect()->route('shift-templates.index')
            ->with('success', 'Shift template deleted successfully.');
    }
}
