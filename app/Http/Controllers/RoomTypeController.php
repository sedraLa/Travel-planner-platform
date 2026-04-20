<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use App\Services\MediaServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoomTypeController extends Controller
{
    /**
     * Edit page (inside hotel context)
     */
    public function edit(Hotel $hotel)
    {
        $hotel->load('roomTypes.images');

        return view('hotel.room-types.edit', compact('hotel'));
    }

    /**
     * Update all room types for a hotel
     */
    public function update(Request $request, Hotel $hotel)
    {
        DB::transaction(function () use ($request, $hotel) {
            $this->syncRoomTypes($hotel, $request);
        });

        return redirect()
            ->route('hotels.room-types.edit', $hotel->id)
            ->with('success', 'Room types updated successfully.');
    }

    /**
     * Delete whole room type
     */
    public function destroy(RoomType $roomType)
    {
        foreach ($roomType->images as $image) {
            Storage::delete('public/' . $image->image_url);
        }

        $roomType->delete();

        return back()->with('success', 'Room type deleted.');
    }

    /**
     * Delete single image
     */
    public function deleteImage($id)
    {
        $image = RoomTypeImage::findOrFail($id);
        $roomType = $image->roomType;
        $wasPrimary = $image->is_primary;

        Storage::delete('public/' . $image->image_url);
        $image->delete();

        if ($roomType && $wasPrimary) {
            $nextImage = $roomType->images()->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Image deleted.');
    }

    /**
     * Set single image as primary for a room type
     */
    public function setPrimaryImage($id)
    {
        $image = RoomTypeImage::findOrFail($id);
        $roomType = $image->roomType;

        if (!$roomType) {
            return back()->withErrors(['error' => 'Room type not found for this image.']);
        }

        $roomType->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set successfully.');
    }

    /**
     * Sync logic (copied from hotel controller but isolated)
     */
    private function syncRoomTypes(Hotel $hotel, Request $request, bool $isUpdate = true): void
    {
        $roomTypesData = $request->input('room_types', []);
        $roomTypeFiles = $request->file('room_types', []);
    
        $submittedIds = collect($roomTypesData)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();
    
        if ($isUpdate) {
            $hotel->roomTypes()
                ->whereNotIn('id', $submittedIds)
                ->with('images')
                ->get()
                ->each(function ($roomType) {
                    foreach ($roomType->images as $img) {
                        Storage::delete('public/' . $img->image_url);
                    }
                    $roomType->delete();
                });
        }
    
        foreach ($roomTypesData as $index => $data) {
    
            $roomType = isset($data['id'])
                ? $hotel->roomTypes()->find($data['id'])
                : null;
    
            $roomType = $roomType
                ? tap($roomType)->update([
                    'name' => $data['name'],
                    'price_per_night' => $data['price_per_night'],
                    'capacity' => $data['capacity'],
                    'quantity' => $data['quantity'],
                ])
                : $hotel->roomTypes()->create([
                    'name' => $data['name'],
                    'price_per_night' => $data['price_per_night'],
                    'capacity' => $data['capacity'],
                    'quantity' => $data['quantity'],
                ]);
    
            // images upload
            $uploadedImages = data_get($roomTypeFiles, "{$index}.images", []);
            $newImageIds = [];
    
            foreach ($uploadedImages as $file) {
                $path = $file->store('room-types', 'public');
    
                $img = $roomType->images()->create([
                    'image_url' => $path,
                    'is_primary' => false,
                ]);
    
                $newImageIds[] = $img->id;
            }
    
            // reset primary
            $roomType->images()->update(['is_primary' => false]);
    
            // existing selection
            $primaryChoice = $data['primary_image_choice'] ?? null;
    
            if ($primaryChoice && str_starts_with($primaryChoice, 'existing:')) {
                $id = (int) str_replace('existing:', '', $primaryChoice);
                $roomType->images()->where('id', $id)->update(['is_primary' => true]);
            }
    
            // new selection
            $newPrimary = $data['primary_new_image_choice'] ?? null;
    
            if ($newPrimary !== null && $newPrimary !== '') {
                $idx = (int) $newPrimary;
    
                if (isset($newImageIds[$idx])) {
                    $roomType->images()->where('id', $newImageIds[$idx])
                        ->update(['is_primary' => true]);
                }
            }
    
            // fallback
            if (!$roomType->images()->where('is_primary', true)->exists()) {
                $first = $roomType->images()->first();
                if ($first) $first->update(['is_primary' => true]);
            }
        }
    }
}
