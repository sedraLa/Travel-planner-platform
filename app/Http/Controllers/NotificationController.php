<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Services\Review\ReviewEligibilityService;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        $eligibilityService = app(ReviewEligibilityService::class);

        $reservationIds = $notifications->getCollection()
            ->pluck('data.reservation_id')
            ->filter()
            ->unique()
            ->values();

            $reviews = Review::where('user_id', auth()->id())
            ->whereIn('reservation_id', $reservationIds)
            ->get();

            $notifications->getCollection()->transform(function ($notification) use ($reviews, $eligibilityService) {

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
            
                    // check eligibility
                    $reservation = $eligibilityService->resolveOwnedReservation(
                        auth()->user(),
                        $data['review_type'],
                        (int)$data['review_id'],
                        $reservationId
                    );
            
                    $data['is_reviewed'] = $isReviewed;
                    $data['can_review'] = $reservation !== null;
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
