<?php

use App\Core\Traits\CurrencyTrait;

use function Tests\privateMethod;
use function Tests\reflection;

beforeEach(function () {
    $this->mock = $this->getMockForTrait(CurrencyTrait::class);
    $this->reflection = reflection($this->mock);
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
