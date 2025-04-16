<?php

namespace App\Enums;

enum OrderType: string
{
    case BUY = 'BUY';
    case SELL = 'SELL';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function valuesInString(): string
    {
        return implode(',', self::values());
    }
}
