<?php

namespace App\Core\Traits;

use App\Core\Models\Offer;
use Illuminate\Support\Collection;

trait OfferTrait
{
    protected function setOffers(): void
    {
        $offers = config('offers');
        $itemsCount = $this->items->groupBy('name')->map->count();

        foreach ($itemsCount as $name => $amount) {
            if (array_key_exists($name, $offers)) {
                $offer = $offers[$name];
                $this->applyOffer($amount, $offer);
            }
        }
    }

    protected function applyOffer(int $amount, array $offer): ?callable
    {
        if ($amount < $offer['should_buy']) return null;

        $item = $this->items->where('name', $offer['item'])->whereNull('offer')->first();
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
        if (!$amount) return null;
        return $this->applyOffer($amount, $offer);
    }
}
