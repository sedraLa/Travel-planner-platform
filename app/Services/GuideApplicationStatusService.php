<?php

namespace App\Services;

use App\Domain\GuideApplication\Factory\GuideApplicationStrategyFactory;
use App\Mail\GuideStatusMail;
use App\Models\Guide;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GuideApplicationStatusService
{
    public function __construct(
        private readonly GuideApplicationStrategyFactory $strategyFactory
    ) {
    }

    public function updateStatus(Guide $guide, string $status): void
    {
        $strategy = $this->strategyFactory->make($status);
        $strategy->apply($guide);

        //send email
        Mail::to($guide->user->email)
            ->send(new GuideStatusMail($guide->user->name, $strategy->status(), $strategy->emailMessage()));

        if ($strategy->shouldDeleteGuide()) {
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
