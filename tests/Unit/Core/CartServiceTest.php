<?php

use App\Core\CartService;
use App\Exceptions\InvalidCurrencyException;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Arr;

use function Tests\reflection;
use function Pest\Faker\faker;

it('sets currency', function () {
    $availableCurrencies = array_keys(config('currencies.available'));
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $setCurrency = $reflection->getMethod('setCurrency');
    $setCurrency->setAccessible(true);

    $currentCurrency = Arr::random($availableCurrencies);
    $setCurrency->invokeArgs($cartService, [$currentCurrency]);
    $currencyProperty = $reflection->getProperty('currency');
    $currencyProperty->setAccessible(true);
    expect($currencyProperty->getValue($cartService))->toBe($currentCurrency);
});

it('throws an excepton if currency is invalid', function () {
    $this->expectException(InvalidCurrencyException::class);
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $setCurrency = $reflection->getMethod('setCurrency');
    $setCurrency->setAccessible(true);

    $setCurrency->invokeArgs($cartService, [faker()->word]);
});

it('sets items', function () {
    $availableItems = array_keys(config('items'));
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $setItems = $reflection->getMethod('setItems');
    $setItems->setAccessible(true);

    $currentItem = Arr::random($availableItems);
    $setItems->invokeArgs($cartService, [[$currentItem]]);

    $currencyProperty = $reflection->getProperty('items');
    $currencyProperty->setAccessible(true);

    $firstItem = $currencyProperty->getValue($cartService)->first();
    expect($firstItem->name)->toBe($currentItem);
    expect($firstItem->price)->toBe(config('items')[$currentItem]['price']);
});

it('throws exception on invalid items', function () {
    $this->expectException(InvalidItemsException::class);
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $setItems = $reflection->getMethod('setItems');
    $setItems->setAccessible(true);

    $setItems->invokeArgs($cartService, [[faker()->word]]);
});
