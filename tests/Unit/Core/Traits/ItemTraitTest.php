<?php

use App\Core\Traits\ItemTrait;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Arr;

use function Pest\Faker\faker;
use function Tests\privateMethod;
use function Tests\reflection;

beforeEach(function () {
    $this->mock = $this->getMockForTrait(ItemTrait::class);
    $this->reflection = reflection($this->mock);
});

it('sets items', function () {
    $availableItems = array_keys(config('items'));

    $makeItems = privateMethod($this->reflection, 'makeItems');
    $currentItem = Arr::random($availableItems);
    $items = $makeItems->invokeArgs($this->mock, [[$currentItem]]);
    $firstItem = $items->first();
    expect($firstItem->name)->toBe($currentItem);
    expect($firstItem->price)->toBe(config('items')[$currentItem]['price']);
});

it('throws exception on invalid items', function () {
    $this->expectException(InvalidItemsException::class);
    $makeItems = privateMethod($this->reflection, 'makeItems');
    $makeItems->invokeArgs($this->mock, [[faker()->word]]);
});
