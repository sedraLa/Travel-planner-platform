<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

use App\Services\Review\ReviewFactoryService;
use App\Services\Review\ReviewEligibilityService;

use App\Models\Hotel;
use App\Models\Trip;
use App\Models\Driver;
use App\Models\Guide;
use App\Models\Review;

class ReviewController extends Controller
{

    public function adminIndex(Request $request)
{
    $query = Review::with(['user', 'reviewable']);

    //  search
    if ($request->search) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%");
        });
    }

    //  filter by type
    if ($request->type) {
        $types = [
            'hotel' => \App\Models\Hotel::class,
            'trip' => \App\Models\Trip::class,
            'guide' => \App\Models\Guide::class,
            'driver' => \App\Models\Driver::class,
        ];

        if (isset($types[$request->type])) {
            $query->where('reviewable_type', $types[$request->type]);
        }
    }

    //  filter rating
    if ($request->rating) {
        $query->where('rating', $request->rating);
    }

    $reviews = $query->latest()->paginate(10);

    return view('reviews.admin-index', compact('reviews'));
}

    //create review

    public function create(Request $request)
{
    return view('reviews.create', [
        'type' => $request->type,
        'id' => $request->id,
        'reservationId' => $request->reservation_id,
    ]);
}

    /**
     * Store new review
     */
    public function store(
        StoreReviewRequest $request,
        ReviewFactoryService $factory,
        ReviewEligibilityService $eligibilityService
    ) {
        
    
        $model = match ($request->type) {
            'hotel' => Hotel::findOrFail($request->id),
            'trip' => Trip::findOrFail($request->id),
            'driver' => Driver::findOrFail($request->id),
            'guide' => Guide::findOrFail($request->id),
        };

        $reservation = $eligibilityService->resolveOwnedReservation(
            $request->user(),
            $request->type,
            (int) $request->id,
            (int) $request->reservation_id
        );

        if (! $reservation) {
            abort(403, 'Unauthorized reservation access.');
        }

        $alreadyReviewed = Review::where('user_id', $request->user()->id)
            ->where('reservation_id', $request->reservation_id)
            ->exists();

        if ($alreadyReviewed) {
            throw ValidationException::withMessages([
                'reservation_id' => 'You have already submitted a review for this reservation.',
            ]);
        }

        try {
            $factory->create($model, $request->only('rating', 'review', 'reservation_id'));
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'reservation_id' => 'You have already submitted a review for this reservation.',
                ]);
            }

            throw $e;
        }
    
        return redirect()->back()->with('success', 'Thanks for your review ❤️');
    }

    /**
     * Update review
     */
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Review updated');
    }

    /**
     * Delete review
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Review deleted');
    }

    public function hotelIndex(Hotel $hotel)
{
    $reviews = $hotel->reviews()
        ->with('user')
        ->latest()
        ->get();

    return view('reviews.hotel-index', compact('hotel', 'reviews'));

    
}

public function tripIndex(string $id)
{
    $trip = Trip::with('reviews.user')->findOrFail($id);

    $reviews = $trip->reviews()->latest()->get();

    return view('reviews.trip-index', compact('trip', 'reviews'));
}

public function guideIndex(string $id)
{
    $guide = Guide::with('user')->findOrFail($id);

    $reviews = $guide->reviews()->with('user')->latest()->get();

    $avg = $guide->reviews()->avg('rating');

    return view('reviews.guide-index', compact('guide', 'reviews', 'avg'));
}

}
