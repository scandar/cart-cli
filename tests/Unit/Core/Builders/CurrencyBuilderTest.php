<?php

use App\Core\Builders\CurrencyBuilder;
use App\Core\Models\Currency;
use App\Exceptions\InvalidCurrencyException;

use function Pest\Faker\faker;

it('makes currency', function () {
    $currencies = collect(config('currencies.available'));
    $currentCurrency = $currencies->random();
    $currency = CurrencyBuilder::make($currentCurrency['name']);

    expect($currency)->toBeInstanceOf(Currency::class);
    expect($currency->name)->toBe($currentCurrency['name']);
    expect($currency->conversionRate)->toBe($currentCurrency['conversion_rate']);
    expect($currency->symbol)->toBe($currentCurrency['symbol']);
    expect($currency->format)->toBe($currentCurrency['format']);
});

it('throws an excepton if currency is invalid', function () {
    $this->expectException(InvalidCurrencyException::class);
    CurrencyBuilder::make(faker()->word);
});
