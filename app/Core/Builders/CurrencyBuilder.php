<?php

namespace App\Core\Builders;

use App\Core\Models\Currency;
use App\Exceptions\InvalidCurrencyException;

class CurrencyBuilder
{
    public static function make(string $currency): Currency
    {
        $currency = strtolower($currency);
        $currencies = config('currencies.available');
        $avaialableCurrencies = array_keys($currencies);

        if (!in_array($currency, $avaialableCurrencies)) {
            $currency = strtoupper($currency);
            $currenciesString = strtoupper(implode(', ', $avaialableCurrencies));
            throw new InvalidCurrencyException("{$currency} is not a valid currency. available currencies ({$currenciesString})");
        }

        return new Currency([
            'name' => $currency,
            'symbol' => $currencies[$currency]['symbol'],
            'format' => $currencies[$currency]['format'],
            'conversionRate' => $currencies[$currency]['conversion_rate'],
        ]);
    }
}
