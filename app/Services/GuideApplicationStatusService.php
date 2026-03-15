<?php

namespace App\Services;

use App\Domain\GuideApplication\Factory\GuideApplicationStateFactory;
use App\Mail\GuideStatusMail;
use App\Models\Guide;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GuideApplicationStatusService
{
    public function __construct(
        private readonly GuideApplicationStateFactory $stateFactory
    ) {
    }

    public function updateStatus(Guide $guide, string $status): void
    {
        $state = $this->stateFactory->make($status);
        $state->apply($guide);

        Mail::to($guide->user->email)
            ->send(new GuideStatusMail($guide->user->name, $state->status(), $state->emailMessage()));

        if ($state->shouldDeleteGuide()) {
            $this->deleteGuideAssets($guide);
            $guide->user()->delete();
            $guide->delete();
        }
    }

    private function deleteGuideAssets(Guide $guide): void
    {
        foreach (['personal_image', 'certificate_image'] as $field) {
            $path = $guide->{$field};

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
