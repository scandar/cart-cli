<?php

use App\Core\CartService;
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
    expect($conversionRateProperty->getValue($cartService))->toBe($currencies[$currentCurrency]);
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
