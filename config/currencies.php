<?php

/*
|--------------------------------------------------------------------------
| currencies and their conversion rate
|--------------------------------------------------------------------------
| conversion_rate: the value to multiply to when converting from the default currency to another currency
| symbol: the currency symbol
| format: is how the number is represented in text form
|   '#' is where the number should appear 'S' is where the symbol should appear
*/

return [
    'default' => 'usd',
    'available' => [
        'usd' => [
            'name' => 'usd',
            'conversion_rate' => 1.0,
            'symbol' => '$',
            'format' => 'S#'
        ],
        'egp' => [
            'name' => 'egp',
            'conversion_rate' => 15.7,
            'symbol' => 'e£',
            'format' => '# S'
        ],
    ],
];
