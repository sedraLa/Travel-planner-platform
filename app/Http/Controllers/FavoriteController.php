<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // عرض كل المفضلات
    
    // إضافة مفضلة
    public function store($type, $id)
    {
        $user = auth()->user();

        if ($type === 'destination') {
            $item = Destination::findOrFail($id);
        } elseif ($type === 'hotel') {
            $item = Hotel::findOrFail($id);
        } else {
            abort(404);
        }

        // toggle
        if ($item->favorites()->where('user_id', $user->id)->exists()) {
            $item->favorites()->where('user_id', $user->id)->delete();
            $status = 'removed';
        } else {
            $item->favorites()->create(['user_id' => $user->id]);
            $status = 'added';
        }


         return response()->json([
        'status' => $status
    ]);

    }
     public function showFavorites()
    {
        $user = Auth::user();

        // Step 1: Get the user's favorite destinations and hotels
        // We will load the 'images' relationship for each of them.
        $favoriteDestinations = $user->favoriteDestinations()->with('images')->get();
        $favoriteHotels = $user->favoriteHotels()->with('images')->get();

        // Step 2: Pass these collections to the view
        return view('favorites.index', [
            'destinations' => $favoriteDestinations,
            'hotels'       => $favoriteHotels,
        ]);
    }
}