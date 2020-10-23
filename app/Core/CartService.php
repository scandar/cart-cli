<?php

namespace App\Core;

use App\Core\Models\Item;
use App\Exceptions\InvalidCurrencyException;
use App\Exceptions\InvalidItemsException;

class CartService
{
    private $currency;
    private $conversionRate;
    private $items;

    public function create(array $items, string $currency)
    {
        $this->setCurrency($currency);
        $this->setItems($items);
        // calculate offers
        // calculate taxes
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

        $this->currency = $currency;
        $this->conversionRate = $currencies[$currency];
    }


    private function setItems(array $itemNames): void
    {
        $itemNames = array_map('strtolower', $itemNames);
        $this->validateItems($itemNames);
        $avaialableItems = config('items');
        $this->items = collect();

        foreach ($itemNames as $name) {
            $itemInfo = $avaialableItems[$name];
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
}
