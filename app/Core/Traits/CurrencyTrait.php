<?php

namespace App\Core\Traits;

trait CurrencyTrait
{
    protected static function convert(float $value, float $conversionRate): float
    {
        return round(($value * $conversionRate) / 100, 4); // 100 to convert from cents to dollars;
    }

    protected static function formatCurrency(float $value, string $format, string $symbol): string
    {
        return str_replace('S', $symbol, str_replace('#', $value, $format));
    }
}
