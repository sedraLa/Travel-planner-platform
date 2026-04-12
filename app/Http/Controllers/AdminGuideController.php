<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Guide;
use Illuminate\Http\Request;
use App\Http\Requests\GuideRequest;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class AdminGuideController extends Controller{


public function index(Request $request)
    {
        $query = Guide::with(['user'])->where('status', 'approved');

        if ($request->filled('search')) {
            $term = $request->search;

            $query->where(function($q) use ($term) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$term%")
                ->orWhere('last_name', 'like', "%$term%")
                ->orWhere('email', 'like', "%$term%"));
            });
        }


        if ($request->filled('country')) {
            $query->whereHas('user', fn($u) => $u->where('country', 'like', "%{$request->country}%"));
        }

        $query->orderBy('date_of_hire', 'desc');

        $guides = $query->get();

        return view('guide.index', compact('guides'));
    }



    public function show(string $id) {
        $guide= Guide::with('user')->findOrFail($id);
      
        return view('guide.show', compact('guide'));
    }




     public function destroy(string $id)
    {
        $guide = Guide::findOrFail($id);

    //delete license image
        if ($guide->certificate_image && Storage::disk('public')->exists($guide->certificate_image)) {
            Storage::disk('public')->delete($guide->certificate_image);
        }


        if ($guide->personal_image && Storage::disk('public')->exists($guide->personal_image)) {
        Storage::disk('public')->delete($guide->personal_image);
    }

        //delete guide
       $guide->user()->delete();

        return redirect()->route('guides.index')->with('success', 'Guide deleted successfully');
    }



    public function guideTrips(Guide $guide)
    {
        $assignments = $guide->assignments()
            ->where('status', 'assigned')
            ->with([
                'trip.primaryDestination',
                'trip.schedules'
            ])
            ->latest()
            ->get();
    
        return view('guide.guide-trips', compact('assignments', 'guide'));
    }


}