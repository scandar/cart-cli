<?php

use App\Core\Traits\TaxTrait;

use function Tests\privateMethod;
use function Tests\reflection;

it('calculates tax', function () {
    $mock = $this->getMockForTrait(TaxTrait::class);
    $reflection = reflection($mock);
    $method = privateMethod($reflection, 'calculateTaxes');
    expect($method->invokeArgs($mock, [100]))->toBe(14.0);
});
