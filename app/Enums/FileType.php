<?php

namespace App\Enums;

enum FileType: string
{
    case CATEGORY = 'categories';
    case PRODUCT = 'products';
    case BRAND = 'brands';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
