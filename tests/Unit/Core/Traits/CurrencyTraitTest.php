<?php

use App\Core\Models\Currency;
use App\Core\Traits\CurrencyTrait;
use App\Exceptions\InvalidCurrencyException;
use Illuminate\Support\Arr;

use function Pest\Faker\faker;
use function Tests\privateMethod;
use function Tests\reflection;

beforeEach(function () {
    $this->mock = $this->getMockForTrait(CurrencyTrait::class);
    $this->reflection = reflection($this->mock);
});

it('sets currency', function () {
    $currencies = config('currencies.available');
    $availableCurrencies = array_keys($currencies);

    $makeCurrency = privateMethod($this->reflection, 'makeCurrency');
    $currentCurrency = Arr::random($availableCurrencies);
    $currency = $makeCurrency->invokeArgs($this->mock, [$currentCurrency]);

    expect($currency)->toBeInstanceOf(Currency::class);
    expect($currency->name)->toBe($currentCurrency);
    expect($currency->conversionRate)->toBe($currencies[$currentCurrency]['conversion_rate']);
    expect($currency->symbol)->toBe($currencies[$currentCurrency]['symbol']);
    expect($currency->format)->toBe($currencies[$currentCurrency]['format']);
});

it('throws an excepton if currency is invalid', function () {
    $this->expectException(InvalidCurrencyException::class);
    $makeCurrency = privateMethod($this->reflection, 'makeCurrency');
    $makeCurrency->invokeArgs($this->mock, [faker()->word]);
});


it('converts currency', function () {
    $currentCurrency = config('currencies.available.egp');
    $convert = privateMethod($this->reflection, 'convert');
    expect($convert->invokeArgs($this->mock, [100, $currentCurrency['conversion_rate']]))->toBe($currentCurrency['conversion_rate']);
});

it('formats currency', function () {
    $formatCurrency = privateMethod($this->reflection, 'formatCurrency');
    expect($formatCurrency->invokeArgs($this->mock, [10.1234, '# S', 'e£']))->toBe('10.1234 e£');
    expect($formatCurrency->invokeArgs($this->mock, [10.1234, 'S#', '$']))->toBe('$10.1234');
});
