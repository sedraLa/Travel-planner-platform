<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Guide;
use App\Models\Specialization;
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
        $specializations = Specialization::all();
        $query = Guide::with(['user','specializations'])->where('status', 'Approved');

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


        if ($request->filled('tour_leader')) {
            $query->where('is_tour_leader', $request->tour_leader);
        }

        if ($request->filled('specialization')) {
            $query->whereHas('specializations', function ($q) use ($request) {
                $q->where('specializations.id', $request->specialization);
            });
        }

        $query->orderBy('date_of_hire', 'desc');

        $guides = $query->get();

        return view('guide.index', compact('guides','specializations'));
    }



    public function show(string $id) {
        $guide= Guide::with('user','specializations')->findOrFail($id);
      
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
        $guide->delete();

        return redirect()->route('guides.index')->with('success', 'Guid deleted successfully');
    }




}