<?php

namespace App\Core\Traits;

use App\Core\Models\Offer;
use Illuminate\Support\Collection;

trait TaxTrait
{
    protected function calculateTaxes(float $amount): float
    {
        return round($amount * config('taxes.vat'), 4);
    }
}
