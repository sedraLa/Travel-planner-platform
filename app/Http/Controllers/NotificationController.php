<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);

        $reservationIds = $notifications->getCollection()
            ->pluck('data.reservation_id')
            ->filter()
            ->unique()
            ->values();

            $reviews = Review::where('user_id', auth()->id())
            ->whereIn('reservation_id', $reservationIds)
            ->get();

            $notifications->getCollection()->transform(function ($notification) use ($reviews) {

            $data = $notification->data; 

            $reservationId = (int) data_get($data, 'reservation_id');

        if (($data['type'] ?? null) === 'review_request') {
            $typeMap = [
                'trip' => \App\Models\Trip::class,
                'guide' => \App\Models\Guide::class,
                'hotel' => \App\Models\Hotel::class,
                'activity' => \App\Models\Activity::class,
                'driver' => \App\Models\Driver::class,
            ];
            
            $currentType = $typeMap[$data['review_type']] ?? null;
            
            $isReviewed = $reviews->contains(function ($review) use ($reservationId, $currentType) {
                return (int)$review->reservation_id === $reservationId
                    && $review->reviewable_type === $currentType;
            });
            
            $data['is_reviewed'] = $isReviewed;
        }

            $notification->data = $data; 

            return $notification;
        });

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead(); 
        return redirect()->back(); 
    }
}
