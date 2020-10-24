<?php

namespace App\Core\Builders;

use App\Core\Models\Currency;
use App\Core\Models\Item;
use App\Core\Models\Output;
use App\Core\Traits\CurrencyTrait;
use Illuminate\Support\Collection;

class OutputBuilder
{
    use CurrencyTrait;

    public static function make(Collection $items, Currency $currency, float $taxes): Output
    {
        $conversionRate = $currency->conversionRate;
        $format = $currency->format;
        $symbol = $currency->symbol;

        $subtotal = self::convert($items->sum('price'), $conversionRate);
        $taxes = self::convert($taxes, $conversionRate);
        $discounts = $items->whereNotNull('offer')->map(function (Item $item) use ($conversionRate) {
            return (object) [
                'title' => $item->offer->title,
                'amount' => self::convert($item->offer->discount, $conversionRate),
            ];
        });
        $total = $subtotal + $taxes - $discounts->sum('amount');

        return new Output([
            'subtotal' => self::formatCurrency($subtotal, $format, $symbol),
            'taxes' => self::formatCurrency($taxes, $format, $symbol),
            'total' => self::formatCurrency($total, $format, $symbol),
            'discounts' =>  $discounts->map(function ($discount) use ($format, $symbol) {
                $formatedDiscount = self::formatCurrency($discount->amount, $format, $symbol);
                return "{$discount->title}: -{$formatedDiscount}";
            }),
        ]);
    }
}
