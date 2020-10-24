<?php

use App\Core\CartService;
use App\Core\Models\Currency;
use App\Core\Models\Item;
use App\Core\Models\Offer;
use App\Core\Models\Output;
use App\Exceptions\InvalidCurrencyException;
use App\Exceptions\InvalidItemsException;
use Illuminate\Support\Arr;

use function Tests\reflection;
use function Tests\privateMethod;
use function Tests\privateProperty;
use function Pest\Faker\faker;

it('sets currency', function () {
    $currencies = config('currencies.available');
    $availableCurrencies = array_keys($currencies);
    $cartService = new CartService();
    $reflection = reflection($cartService);

    $makeCurrency = privateMethod($reflection, 'makeCurrency');
    $currentCurrency = Arr::random($availableCurrencies);
    $currency = $makeCurrency->invokeArgs($cartService, [$currentCurrency]);

    expect($currency)->toBeInstanceOf(Currency::class);
    expect($currency->name)->toBe($currentCurrency);
    expect($currency->conversionRate)->toBe($currencies[$currentCurrency]['conversion_rate']);
    expect($currency->symbol)->toBe($currencies[$currentCurrency]['symbol']);
    expect($currency->format)->toBe($currencies[$currentCurrency]['format']);
});

it('throws an excepton if currency is invalid', function () {
    $this->expectException(InvalidCurrencyException::class);
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $makeCurrency = privateMethod($reflection, 'makeCurrency');
    $makeCurrency->invokeArgs($cartService, [faker()->word]);
});

it('sets items', function () {
    $availableItems = array_keys(config('items'));
    $cartService = new CartService();
    $reflection = reflection($cartService);

    $makeItems = privateMethod($reflection, 'makeItems');
    $currentItem = Arr::random($availableItems);
    $items = $makeItems->invokeArgs($cartService, [[$currentItem]]);
    $firstItem = $items->first();
    expect($firstItem->name)->toBe($currentItem);
    expect($firstItem->price)->toBe(config('items')[$currentItem]['price']);
});

it('throws exception on invalid items', function () {
    $this->expectException(InvalidItemsException::class);
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $makeItems = privateMethod($reflection, 'makeItems');
    $makeItems->invokeArgs($cartService, [[faker()->word]]);
});

it('calculates and sets offers on items', function () {
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $itemsProperty = privateProperty($reflection, 'items');
    $items = collect([
        new Item(['name' => 'shoes', 'price' => 1000]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 'jacket', 'price' => 2000]),
    ]);

    $itemsProperty->setValue($cartService, $items);
    $setOffers = privateMethod($reflection, 'setOffers');
    $setOffers->invoke($cartService);

    $itemsCollection = $itemsProperty->getValue($cartService);
    $jacket = $itemsCollection->where('name', 'jacket')->first();
    expect($jacket->offer)->toBeInstanceOf(Offer::class);
    expect($jacket->offer->percent)->toBe(0.50);
    expect($jacket->offer->discount)->toBe($jacket->price * 0.50);
    expect($jacket->offer->title)->toBe("50% off jacket");

    $shoes = $itemsCollection->where('name', 'shoes')->first();
    expect($shoes->offer)->toBeInstanceOf(Offer::class);
    expect($shoes->offer->percent)->toBe(0.10);
    expect($shoes->offer->discount)->toBe($shoes->price * 0.10);
    expect($shoes->offer->title)->toBe("10% off shoes");

    expect($itemsCollection->where('name', 't-shirt')->first()->offer)->toBeNull();
});

it('sets taxes', function () {
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $calculateTaxes = privateMethod($reflection, 'calculateTaxes');
    expect($calculateTaxes->invokeArgs($cartService, [100]))->toBe(14.0);
});

it('formats output', function () {
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $itemsProperty = privateProperty($reflection, 'items');
    $items = collect([
        new Item(['name' => 'shoes', 'price' => 1000]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 't-shirt', 'price' => 1100]),
        new Item(['name' => 'jacket', 'price' => 2000]),
    ]);

    $itemsProperty->setValue($cartService, $items);

    $setOffers = privateMethod($reflection, 'setOffers');
    $setOffers->invoke($cartService);

    $currentCurrency = Arr::random(config('currencies.available'));
    $currencyProperty = privateProperty($reflection, 'currency');
    $currencyProperty->setValue($cartService, new Currency([
        'name' => $currentCurrency['name'],
        'conversionRate' => $currentCurrency['conversion_rate'],
        'symbol' => $currentCurrency['symbol'],
        'format' => $currentCurrency['format'],
    ]));

    $taxesProperty = privateProperty($reflection, 'taxes');
    $taxesProperty->setValue($cartService, round($items->sum('price') * config('taxes.vat'), 4));

    $formatOutput = privateMethod($reflection, 'getOutput');
    $output = $formatOutput->invoke($cartService);

    expect($output)->toBeInstanceOf(Output::class);
});

it('converts currency', function () {
    $cartService = new CartService();
    $reflection = reflection($cartService);
    $currentCurrency = config('currencies.available.egp');

    $convert = privateMethod($reflection, 'convert');
    expect($convert->invokeArgs($cartService, [100, $currentCurrency['conversion_rate']]))->toBe($currentCurrency['conversion_rate']);
});

it('formats currency', function () {
    $cartService = new CartService();
    $reflection = reflection($cartService);

    $formatCurrency = privateMethod($reflection, 'formatCurrency');
    expect($formatCurrency->invokeArgs($cartService, [10.1234, '# S', 'e£']))->toBe('10.1234 e£');
    expect($formatCurrency->invokeArgs($cartService, [10.1234, 'S#', '$']))->toBe('$10.1234');
});
