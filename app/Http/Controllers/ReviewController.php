<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Http\Request;

use App\Services\Review\ReviewFactoryService;
use App\Services\Review\ReviewEligibilityService;

use App\Models\Hotel;
use App\Models\Trip;
use App\Models\Driver;
use App\Models\Guide;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Store new review
     */
    public function store(
        StoreReviewRequest $request,
        ReviewFactoryService $factory
    ) {
        $model = match ($request->type) {
            'hotel' => Hotel::findOrFail($request->id),
            'trip' => Trip::findOrFail($request->id),
            'driver' => Driver::findOrFail($request->id),
            'guide' => Guide::findOrFail($request->id),
        };
    
        $factory->create($model, $request->only('rating', 'review'));
    
        return back()->with('success', 'Review submitted');
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
}