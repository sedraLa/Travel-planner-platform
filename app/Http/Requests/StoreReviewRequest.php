<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
    
        if (!$user) {
            return false;
        }
    
        return app(\App\Services\Review\ReviewEligibilityService::class)
            ->canReview($user, $this->type, $this->id);
    }
}