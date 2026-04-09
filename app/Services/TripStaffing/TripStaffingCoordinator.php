<?php

namespace App\Services\TripStaffing;

use App\Models\GuideAssignment;
use App\Models\GuideRequest;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripStaffingAdminNotification;
use App\Services\Trip\TripStateManager;
use Illuminate\Support\Facades\DB;

class TripStaffingCoordinator
{
    public function __construct(private TripStateManager $stateManager)
    {
    }

    public function acceptGuideRequest(GuideRequest $request): bool
    {
        return DB::transaction(function () use ($request) {
            $request = GuideRequest::query()->lockForUpdate()->find($request->id);
            $trip = Trip::query()->lockForUpdate()->find($request?->trip_id);

            if (! $request || ! $trip || $request->status !== 'pending' || $trip->assigned_guide_id) {
                return false;
            }

            $request->update(['status' => 'accepted', 'responded_at' => now()]);
            $trip->update(['assigned_guide_id' => $request->guide_id]);

            GuideAssignment::query()->updateOrCreate(
                ['trip_id' => $trip->id, 'guide_id' => $request->guide_id],
                ['status' => 'assigned']
            );

            $trip->guideRequests()->where('id', '!=', $request->id)->where('status', 'pending')->update(['status' => 'expired']);

            $this->notifyAdmins("Guide assigned to trip #{$trip->id}.");
            $this->progressTripIfFullyStaffed($trip->fresh());

            return true;
        });
    }

    public function rejectGuideRequest(GuideRequest $request): void
    {
        $request->update(['status' => 'rejected', 'responded_at' => now()]);
    }

    public function failTripStaffing(Trip $trip, string $reason): void
    {
        if ($trip->status !== 'staffing_in_progress') {
            return;
        }

        $this->stateManager->transition($trip, 'draft');
        $this->notifyAdmins("Trip #{$trip->id} reverted to draft: {$reason}");
    }

    public function finalizeInitialMatchingOutcome(Trip $trip): void
    {
        $trip = $trip->fresh();

        if (! $trip || $trip->status !== 'staffing_in_progress') {
            return;
        }

        if ($trip->ranked_guide_ids === null) {
            return;
        }

        $rankedGuideIds = $trip->ranked_guide_ids ?? [];

        if (empty($rankedGuideIds)) {
            $this->failTripStaffing($trip, 'No available guides accepted requirements.');
        }
    }

    private function progressTripIfFullyStaffed(Trip $trip): void
    {
        if (! $trip->assigned_guide_id) {
            return;
        }

        if ($trip->status === 'staffing_in_progress') {
            $this->stateManager->transition($trip, 'staffed');
        }

        if ($trip->fresh()->status === 'staffed') {
            $this->stateManager->transition($trip, 'published');
        }
    }

    private function notifyAdmins(string $message): void
    {
        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(fn (User $admin) => $admin->notify(new TripStaffingAdminNotification($message)));
    }
}
