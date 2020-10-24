<?php

namespace App\Core\Builders;

use App\Core\Models\Item;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Collection;

class ItemsBuilder
{
    public static function make(array $itemNames): Collection
    {
        $itemNames = array_map('strtolower', $itemNames);
        self::validateItems($itemNames);
        $availableItems = config('items');
        $items = collect();

        foreach ($itemNames as $name) {
            $itemInfo = $availableItems[$name];
            $item = new Item(['name' => $name, 'price' => $itemInfo['price']]);
            $items->push($item);
        }

        return $items;
    }

    protected static function validateItems(array $itemNames): void
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
