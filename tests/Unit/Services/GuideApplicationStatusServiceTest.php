<?php

namespace Tests\Unit\Services;

use App\Domain\GuideApplication\Factory\GuideApplicationStateFactory;
use App\Mail\GuideStatusMail;
use App\Models\Guide;
use App\Services\GuideApplicationStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuideApplicationStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_approves_guide_and_sends_email(): void
    {
        Mail::fake();

        $guide = Guide::factory()->create([
            'status' => 'pending',
            'date_of_hire' => null,
        ]);

        $service = new GuideApplicationStatusService(new GuideApplicationStateFactory());
        $service->updateStatus($guide, 'approved');

        $guide->refresh();

        $this->assertSame('approved', $guide->status);
        $this->assertNotNull($guide->date_of_hire);

        Mail::assertSent(GuideStatusMail::class, 1);
    }

    public function test_it_rejects_guide_sends_email_and_deletes_related_records_and_files(): void
    {
        Mail::fake();
        Storage::fake('public');

        Storage::disk('public')->put('guides/profile.jpg', 'img');
        Storage::disk('public')->put('certificates/sample.jpg', 'img');

        $guide = Guide::factory()->create([
            'status' => 'pending',
            'personal_image' => 'guides/profile.jpg',
            'certificate_image' => 'certificates/sample.jpg',
        ]);

        $userId = $guide->user_id;
        $guideId = $guide->id;

        $service = new GuideApplicationStatusService(new GuideApplicationStateFactory());
        $service->updateStatus($guide, 'rejected');

        $this->assertDatabaseMissing('guides', ['id' => $guideId]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);

        Storage::disk('public')->assertMissing('guides/profile.jpg');
        Storage::disk('public')->assertMissing('certificates/sample.jpg');

        Mail::assertSent(GuideStatusMail::class, 1);
    }
}
