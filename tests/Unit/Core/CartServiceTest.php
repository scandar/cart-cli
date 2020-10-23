<?php

use App\Core\CartService;
use App\Core\Models\Item;
use App\Core\Models\Offer;
use App\Exceptions\InvalidCurrencyException;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Arr;

use function Tests\reflection;
use function Tests\privateMethod;
use function Tests\privateProperty;
use function Pest\Faker\faker;

it('sets currency', function () {
    $currencies = config('currencies.available');
    $availableCurrencies = array_keys($currencies);
    $cartService = new CartService();
    $reflection = reflection($cartService);

    $setCurrency = privateMethod($reflection, 'setCurrency');
    $currentCurrency = Arr::random($availableCurrencies);
    $setCurrency->invokeArgs($cartService, [$currentCurrency]);

    $currencyProperty = privateProperty($reflection, 'currency');
    expect($currencyProperty->getValue($cartService))->toBe($currentCurrency);

    $conversionRateProperty = privateProperty($reflection, 'conversionRate');
    expect($conversionRateProperty->getValue($cartService))->toBe((float) $currencies[$currentCurrency]);
});

it('throws an excepton if currency is invalid', function () {
    $this->expectException(InvalidCurrencyException::class);
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $setCurrency = privateMethod($reflection, 'setCurrency');
    $setCurrency->invokeArgs($cartService, [faker()->word]);
});

it('sets items', function () {
    $availableItems = array_keys(config('items'));
    $cartService = new CartService();
    $reflection = reflection($cartService);

    $setItems = privateMethod($reflection, 'setItems');
    $currentItem = Arr::random($availableItems);
    $setItems->invokeArgs($cartService, [[$currentItem]]);

    $itemsProperty = privateProperty($reflection, 'items');
    $firstItem = $itemsProperty->getValue($cartService)->first();
    expect($firstItem->name)->toBe($currentItem);
    expect($firstItem->price)->toBe(config('items')[$currentItem]['price']);
});

it('throws exception on invalid items', function () {
    $this->expectException(InvalidItemsException::class);
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $setItems = privateMethod($reflection, 'setItems');
    $setItems->invokeArgs($cartService, [[faker()->word]]);
});

it('calculates and sets offers on items', function () {
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $itemsProperty = privateProperty($reflection, 'items');
    $items = collect([
        new Item(['name' => 'shoes', 'price' => 1000]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 'jacket', 'price' => 2000]),
    ]);

    $itemsProperty->setValue($cartService, $items);
    $setOffers = privateMethod($reflection, 'setOffers');
    $setOffers->invokeArgs($cartService, []);

    $itemsCollection = $itemsProperty->getValue($cartService);
    expect($itemsCollection->where('name', 'jacket')->first()->offer)->toBeInstanceOf(Offer::class);
    expect($itemsCollection->where('name', 'jacket')->first()->offer->percent)->toBe(50.0);

    expect($itemsCollection->where('name', 'shoes')->first()->offer)->toBeInstanceOf(Offer::class);
    expect($itemsCollection->where('name', 'shoes')->first()->offer->percent)->toBe(10.0);

    expect($itemsCollection->where('name', 't-shirt')->first()->offer)->toBeNull();
});
