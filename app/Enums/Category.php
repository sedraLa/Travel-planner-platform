<?php

namespace App\Enums;

enum Category: string
{
    case CULTURE = 'culture';
    case NATURE = 'nature';
    case SHOPPING = 'shopping';
    case SPORTS = 'sports';
    case ENTERTAINMENT = 'entertainment';
    case FAMILY = 'family';
    case ROMANCE = 'romance';
    case ADVENTURE = 'adventure';
    case WELLNESS = 'wellness';
    case FOOD = 'food';

    public static function values(): array
    {
        return array_map(fn (self $category) => $category->value, self::cases());
    }
}
