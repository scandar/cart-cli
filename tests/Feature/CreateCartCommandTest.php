<?php

it('creates cart and applies offers', function () {
    $this->artisan('create T-Shirt T-Shirt Shoes Jacket')
        ->expectsOutput('Subtotal: $66.96
                        Taxes: $9.37
                        Discounts:
                            10% off shoes: -$2.499
                            50% off jacket: -$9.995
                        Total: $63.8404')
        ->assertExitCode(0);
});

it('creates cart with EGP as the currency', function() {
    $this->artisan('create T-Shirt Pants --bill-currency=EGP')
    ->expectsOutput('Subtotal: 409 e£
                    Taxes: 57 e£
                    Total: 467 e£')
    ->assertExitCode(0);
});
