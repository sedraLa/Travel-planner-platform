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


     public function create()
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('shifts.shiftcreate', compact('days'));
    }


    public function store(ShiftTemplateRequest $request)
    {
        ShiftTemplate::create($request->validated());

        return redirect()->route('shift-templates.index')
            ->with('success', 'Shift template created successfully.');
    }


     public function destroy(string $id)
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        
        $shiftTemplate = ShiftTemplate::findOrFail($id);
        $shiftTemplate->delete();
        

        
        return redirect()->route('shift-templates.index')
            ->with('success', 'Shift template deleted successfully.');
    }
}
