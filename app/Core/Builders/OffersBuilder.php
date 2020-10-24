<?php

namespace App\Core\Builders;

use App\Core\Models\Offer;
use Illuminate\Support\Collection;

class OffersBuilder
{
    public static function make(Collection $items): Collection
    {
        $offers = config('offers');
        $items = $items->map(fn ($item) => clone $item);
        $itemsCount = $items->groupBy('name')->map->count();

        foreach ($itemsCount as $name => $amount) {
            if (array_key_exists($name, $offers)) {
                $offer = $offers[$name];
                self::applyOffer($amount, $offer, $items);
            }
        }

        return $items;
    }

    public static function applyOffer(int $amount, array $offer, Collection $items): ?Collection
    {
        if ($amount < $offer['should_buy']) return null;

        $item = $items->where('name', $offer['item'])->whereNull('offer')->first();
        if (!$item) return null;

        $titlePercentage = $offer['discount_percent'] * 100;
        $item->setProp(
            'offer',
            new Offer([
                'title' => "{$titlePercentage}% off {$offer['item']}",
                'percent' => $offer['discount_percent'],
                'discount' => $item->price * $offer['discount_percent'],
            ])
        );

        $amount = $amount - $offer['should_buy'];
        if (!$amount) return $items;
        return self::applyOffer($amount, $offer, $items);
    }
}
