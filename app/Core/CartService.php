<?php

namespace App\Core;

use App\Core\Models\Currency;
use App\Core\Models\Item;
use App\Core\Models\Output;
use App\Core\Traits\CurrencyTrait;
use App\Core\Traits\ItemTrait;
use App\Core\Traits\OfferTrait;
use App\Core\Traits\TaxTrait;
use Illuminate\Support\Collection;

class CartService
{
    use CurrencyTrait, ItemTrait, OfferTrait, TaxTrait;

    private ?Currency $currency;
    private ?Collection $items;
    private float $taxes;

    public function create(array $items, string $currency): Output
    {
        $this->currency = $this->makeCurrency($currency);
        $this->items = $this->makeItems($items);
        $this->taxes = $this->calculateTaxes($this->items->sum('price'));
        $this->setOffers();
        return $this->getOutput();
    }

    protected function getOutput(): Output
    {
        $conversionRate = $this->currency->conversionRate;
        $format = $this->currency->format;
        $symbol = $this->currency->symbol;

        $subtotal = $this->convert($this->items->sum('price'), $conversionRate);
        $taxes = $this->convert($this->taxes, $conversionRate);
        $discounts = $this->items->whereNotNull('offer')->map(function (Item $item) use ($conversionRate) {
            return (object) [
                'title' => $item->offer->title,
                'amount' => $this->convert($item->offer->discount, $conversionRate),
            ];
        });
        $total = $subtotal + $taxes - $discounts->sum('amount');

        return new Output([
            'subtotal' => $this->formatCurrency($subtotal, $format, $symbol),
            'taxes' => $this->formatCurrency($taxes, $format, $symbol),
            'total' => $this->formatCurrency($total, $format, $symbol),
            'discounts' =>  $discounts->map(function ($discount) use ($format, $symbol) {
                return "{$discount->title}: -{$this->formatCurrency($discount->amount,$format,$symbol)}";
            }),
        ]);
    }
}
