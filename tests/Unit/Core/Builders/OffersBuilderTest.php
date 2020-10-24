<?php

use App\Core\Builders\OffersBuilder;
use App\Core\Models\Item;
use App\Core\Models\Offer;

it('calculates and sets offers on items', function () {
    $items = collect([
        new Item(['name' => 'shoes', 'price' => 1000]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 'jacket', 'price' => 2000]),
    ]);

    $itemsWithOffers = OffersBuilder::make($items);

    $jacket = $itemsWithOffers->where('name', 'jacket')->first();
    expect($jacket->offer)->toBeInstanceOf(Offer::class);
    expect($jacket->offer->percent)->toBe(0.50);
    expect($jacket->offer->discount)->toBe($jacket->price * 0.50);
    expect($jacket->offer->title)->toBe("50% off jacket");

    $shoes = $itemsWithOffers->where('name', 'shoes')->first();
    expect($shoes->offer)->toBeInstanceOf(Offer::class);
    expect($shoes->offer->percent)->toBe(0.10);
    expect($shoes->offer->discount)->toBe($shoes->price * 0.10);
    expect($shoes->offer->title)->toBe("10% off shoes");

    expect($itemsWithOffers->where('name', 't-shirt')->first()->offer)->toBeNull();
});
