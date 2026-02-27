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

   
}