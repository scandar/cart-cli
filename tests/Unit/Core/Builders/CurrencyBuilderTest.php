<?php

use App\Core\Builders\CurrencyBuilder;
use App\Core\Models\Currency;
use App\Exceptions\InvalidCurrencyException;
use Illuminate\Support\Arr;

use function Pest\Faker\faker;

it('makes currency', function () {
    $currencies = config('currencies.available');
    $availableCurrencies = array_keys($currencies);
    $currentCurrency = Arr::random($availableCurrencies);
    $currency = CurrencyBuilder::make($currentCurrency);

    expect($currency)->toBeInstanceOf(Currency::class);
    expect($currency->name)->toBe($currentCurrency);
    expect($currency->conversionRate)->toBe($currencies[$currentCurrency]['conversion_rate']);
    expect($currency->symbol)->toBe($currencies[$currentCurrency]['symbol']);
    expect($currency->format)->toBe($currencies[$currentCurrency]['format']);
});

it('throws an excepton if currency is invalid', function () {
    $this->expectException(InvalidCurrencyException::class);
    CurrencyBuilder::make(faker()->word);
});
