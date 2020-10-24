<?php

it('creates cart and applies offers', function () {
    $this->artisan('create T-Shirt T-Shirt Shoes Jacket')
        ->expectsOutput('Subtotal: $66.96')
        ->expectsOutput('Taxes: $9.3744')
        ->expectsOutput('Discounts:')
        ->expectsOutput('   10% off shoes: -$2.499')
        ->expectsOutput('   50% off jacket: -$9.995')
        ->expectsOutput('Total: $63.8404')
        ->assertExitCode(0);
});

it('creates cart with EGP as the currency', function () {
    $this->artisan('create T-Shirt Pants --bill-currency=EGP')
        ->expectsOutput('Subtotal: 407.886 e£')
        ->expectsOutput('Taxes: 57.104 e£')
        ->expectsOutput('Total: 464.99 e£')
        ->assertExitCode(0);
});
