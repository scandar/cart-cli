<?php

namespace App\Core\Builders;

use App\Core\Models\Currency;
use App\Exceptions\InvalidCurrencyException;

class CurrencyBuilder
{
    public static function make(string $currency): Currency
    {
        $currency = strtolower($currency);
        $currencies = collect(config('currencies.available'));
        $avaialableCurrencies = $currencies->pluck('name');

        if (!$avaialableCurrencies->contains($currency)) {
            $currency = strtoupper($currency);
            $currenciesString = strtoupper(implode(', ', $avaialableCurrencies->toArray()));
            throw new InvalidCurrencyException("{$currency} is not a valid currency. available currencies ({$currenciesString})");
        }

        $currency = $currencies->where('name', $currency)->first();
        return new Currency([
            'name' => $currency['name'],
            'symbol' => $currency['symbol'],
            'format' => $currency['format'],
            'conversionRate' => $currency['conversion_rate'],
        ]);
    }
}
