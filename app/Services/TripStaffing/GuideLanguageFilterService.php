<?php

namespace App\Services\TripStaffing;

use App\Models\Guide;
use Illuminate\Support\Collection;

class GuideLanguageFilterService
{
    public function filterByLanguage(Collection $guides, array $languageTerms): Collection
    {
        if (empty($languageTerms)) {
            return collect();
        }

        $terms = collect($languageTerms)
            ->map(fn (string $term) => trim(mb_strtolower($term)))
            ->filter()
            ->unique()
            ->values();

        if ($terms->isEmpty()) {
            return collect();
        }

        $languageMatchedIds = Guide::query()
            ->whereIn('id', $guides->pluck('id')->all())
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhereRaw('LOWER(TRIM(languages)) LIKE ?', ['%' . $term . '%']);
                }
            })
            ->pluck('id')
            ->all();

        return $guides->whereIn('id', $languageMatchedIds)->values();
    }
}
