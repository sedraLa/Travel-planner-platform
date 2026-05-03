<?php

namespace App\Services\AiTrip;

use App\Models\Trip;
use App\Models\TripImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TripMediaService
{
    public function saveImages(Trip $trip, array $payload, ?UploadedFile $coverImageFile = null, array $imageFiles = []): void
    {
        DB::transaction(function () use ($trip, $payload, $coverImageFile, $imageFiles) {
            $trip->images()->delete();

            //get current cover image
            $coverImagePath = $payload['cover_existing_path'] ?? null;
            //new cover image
            if ($coverImageFile) {
                $coverImagePath = '/storage/' . Storage::disk('public')->put('trips', $coverImageFile);
            }
            //save cover image
            if (! empty($coverImagePath)) {
                TripImage::create([
                    'trip_id' => $trip->id,
                    'image_path' => $coverImagePath,
                    'is_cover' => true,
                ]);
            }

            //process other images
            foreach (($payload['images'] ?? []) as $index => $imagePayload) {
                //old images
                $imagePath = $imagePayload['existing_path'] ?? null;

                //new images
                if (($imageFiles[$index]['image_file'] ?? null) instanceof UploadedFile) {
                    $imagePath = '/storage/' . Storage::disk('public')->put('trips', $imageFiles[$index]['image_file']);
                }

                if (blank($imagePath)) {
                    continue;
                }

                TripImage::create([
                    'trip_id' => $trip->id,
                    'image_path' => $imagePath,
                    'is_cover' => false,
                ]);
            }
        });
    }
}
