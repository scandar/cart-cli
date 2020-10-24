<?php

namespace App\Core\Traits;

trait TaxTrait
{
    protected static function calculateTaxes(float $amount): float
    {
        return round($amount * config('taxes.vat'), 4);
    }
}
