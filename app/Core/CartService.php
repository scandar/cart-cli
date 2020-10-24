<?php

namespace App\Core;

use App\Core\Builders\CurrencyBuilder;
use App\Core\Builders\ItemsBuilder;
use App\Core\Builders\OffersBuilder;
use App\Core\Builders\OutputBuilder;
use App\Core\Models\Currency;
use App\Core\Models\Output;
use App\Core\Traits\TaxTrait;
use Illuminate\Support\Collection;

class CartService
{
    use TaxTrait;

    private ?Currency $currency;
    private ?Collection $items;
    private float $taxes;

    public function create(array $items, string $currency): Output
    {
        $this->currency = CurrencyBuilder::make($currency);
        $this->items = ItemsBuilder::make($items);
        $this->taxes = self::calculateTaxes($this->items->sum('price'));
        $this->items = OffersBuilder::make($this->items);
        return OutputBuilder::make($this->items, $this->currency, $this->taxes);
    }
}
