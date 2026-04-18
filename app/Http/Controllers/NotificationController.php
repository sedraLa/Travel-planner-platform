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

        $reviewedReservationIds = Review::where('user_id', auth()->id())
            ->whereIn('reservation_id', $reservationIds)
            ->pluck('reservation_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $notifications->getCollection()->transform(function ($notification) use ($reviewedReservationIds) {
            $reservationId = (int) data_get($notification->data, 'reservation_id');

            if (($notification->data['type'] ?? null) === 'review_request') {
                $notification->data['is_reviewed'] = in_array($reservationId, $reviewedReservationIds, true);
            }

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
