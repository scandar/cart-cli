<?php

use App\Core\CartService;
use App\Core\Models\Output;

it('creates a cart with EGP currency', function () {
    $cartService = new CartService();
    $output = $cartService->create(['t-shirt', 't-shirt', 'shoes', 'jacket'], 'EGP');
    expect($output)->toBeInstanceOf(Output::class);
    expect($output->subtotal)->toBe('1051.272 e£');
    expect($output->taxes)->toBe('147.1781 e£');
    expect($output->total)->toBe('1002.2943 e£');

    $discounts = array_merge($output->discounts->toArray()); // to reset array index
    expect($discounts[0])->toBe('10% off shoes: -39.2343 e£');
    expect($discounts[1])->toBe('50% off jacket: -156.9215 e£');
});

it('creates a cart with USD currency', function () {
    $cartService = new CartService();
    $output = $cartService->create(['t-shirt', 't-shirt', 'shoes', 'jacket'], 'USD');
    expect($output)->toBeInstanceOf(Output::class);
    expect($output->subtotal)->toBe('$66.96');
    expect($output->taxes)->toBe('$9.3744');
    expect($output->total)->toBe('$63.8404');

    $discounts = array_merge($output->discounts->toArray()); // to reset array index
    expect($discounts[0])->toBe('10% off shoes: -$2.499');
    expect($discounts[1])->toBe('50% off jacket: -$9.995');
});
