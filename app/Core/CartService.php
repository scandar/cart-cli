<?php

namespace App\Core;

use App\Core\Models\Currency;
use App\Core\Models\Item;
use App\Core\Models\Offer;
use App\Exceptions\InvalidCurrencyException;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Collection;

class CartService
{
    private ?Currency $currency;
    private ?Collection $items;
    private float $taxes;

    public function create(array $items, string $currency)
    {
        $this->setCurrency($currency);
        $this->setItems($items);
        $this->setOffers();
        $this->setTaxes();
        // format output
    }

    private function setCurrency(string $currency): void
    {
        $currency = strtolower($currency);
        $currencies = config('currencies.available');
        $avaialableCurrencies = array_keys($currencies);

        if (!in_array($currency, $avaialableCurrencies)) {
            $currency = strtoupper($currency);
            $currenciesString = strtoupper(implode(', ', $avaialableCurrencies));
            throw new InvalidCurrencyException("{$currency} is not a valid currency. available currencies ({$currenciesString})");
        }

        $this->currency = new Currency([
            'name' => $currency,
            'symbol' => $currencies[$currency]['symbol'],
            'format' => $currencies[$currency]['format'],
            'conversionRate' => $currencies[$currency]['conversion_rate']
        ]);
    }

    private function setItems(array $itemNames): void
    {
        $itemNames = array_map('strtolower', $itemNames);
        $this->validateItems($itemNames);
        $availableItems = config('items');
        $this->items = collect();

        foreach ($itemNames as $name) {
            $itemInfo = $availableItems[$name];
            $item = new Item(['name' => $name, 'price' => $itemInfo['price']]);
            $this->items->push($item);
        }
    }

    private function validateItems(array $itemNames): void
    {
        $avaialableItems = array_keys(config('items'));
        $intersect = array_intersect($itemNames, $avaialableItems);
        $invalidItems = array_diff($itemNames, $intersect);

        if (!empty($invalidItems)) {
            $invalidItemsString = implode(', ', $invalidItems);
            throw new InvalidItemsException("Sorry, but these items are not available. ({$invalidItemsString})");
        }
    }

    private function setOffers(): void
    {
        $offers = config('offers');
        $items = $this->items->groupBy('name')->map->count();

        foreach ($items as $name => $amount) {
            if (array_key_exists($name, $offers)) {
                $offer = $offers[$name];
                $this->applyOffer($amount, $offer);
            }
        }
    }

    private function applyOffer(int $amount, array $offer): ?callable
    {
        if ($amount < $offer['should_buy']) return null;

        $item = $this->items->where('name', $offer['item'])->whereNull('offer')->first();
        if (!$item) return null;

        $item->setProp(
            'offer',
            new Offer([
                'title' => "{$offer['discount_percent']}% off {$offer['item']}",
                'percent' => $offer['discount_percent']
            ])
        );

        $amount = $amount - $offer['should_buy'];
        if (!$amount) return null;
        return $this->applyOffer($amount, $offer);
    }

    private function setTaxes(): void
    {
        $this->taxes = $this->items->sum('price') * config('taxes.vat');
    }
}
