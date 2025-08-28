<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // عرض كل المفضلات
    public function index()
    {
        $user = auth()->user();

        return view('favorites.index', [
            'destinations' => $user->favoriteDestinations,
            'hotels'       => $user->favoriteHotels,
        ]);
    }

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

        return back()->with('status', "Favorite $status successfully.");
    }
}
