<?php

namespace App\Core\Traits;

use App\Core\Models\Currency;
use App\Exceptions\InvalidCurrencyException;

trait CurrencyTrait
{
    protected function makeCurrency(string $currency): Currency
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

    protected function convert(float $value, float $conversionRate): float
    {
        return round(($value * $conversionRate) / 100, 4); // 100 to convert from cents to dollars;
    }

    protected function formatCurrency(float $value, string $format, string $symbol): string
    {
        return str_replace('S', $symbol, str_replace('#', $value, $format));
    }
}
