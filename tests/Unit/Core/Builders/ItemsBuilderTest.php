<?php

use App\Core\Builders\ItemsBuilder;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Arr;

use function Pest\Faker\faker;

it('sets items', function () {
    $availableItems = array_keys(config('items'));
    $currentItem = Arr::random($availableItems);
    $items = ItemsBuilder::make([$currentItem]);
    $firstItem = $items->first();
    expect($firstItem->name)->toBe($currentItem);
    expect($firstItem->price)->toBe(config('items')[$currentItem]['price']);
});

it('throws exception on invalid items', function () {
    $this->expectException(InvalidItemsException::class);
    ItemsBuilder::make([faker()->word]);
});
