<?php

/*
|--------------------------------------------------------------------------
| Offers
|--------------------------------------------------------------------------
|
| Contains items (as keys) which are a requirement for an offer
| should_buy: the amount the user should buy from a certain item to get the offer
| discount_percent: the percent deducted from the price of the discounted item
| item: the name of the discounted item
|
*/

return [
    'shoes' => [
        'should_buy' => 1,
        'discount_percent' => 10.0,
        'item' => 'shoes',
    ],
    't-shirt' => [
        'should_buy' => 2,
        'discount_percent' => 50.0,
        'item' => 'jacket',
    ],
];
