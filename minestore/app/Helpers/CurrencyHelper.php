<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format money for the short display
     * @param float $amount
     * @return string
     */
    public static function formatMoney(?float $amount): string
    {
        if ($amount === null) {
            return 0;
        }

        if ($amount < 1000) {
            return (string)$amount;
        }

        if ($amount < 1_000_000) {
            if ($amount % 1000 == 0) {
                return ($amount / 1000) . 'k';
            }
            if ($amount < 100_000) {
                return floor($amount / 100) / 10 . 'k';
            }
            return floor($amount / 1000) . 'k';
        }

        if ($amount < 999_999_999) {
            if ($amount % 1_000_000 == 0) {
                return ($amount / 1_000_000) . 'm';
            }
            if ($amount < 10_000_000) {
                return floor($amount / 100_000) / 10 . 'm';
            }
            return floor($amount / 1_000_000) . 'm';
        }

        return '1b+';
    }

    public static function parseFormattedMoney(string $formattedAmount): float
    {
        $formattedAmount = strtolower($formattedAmount);
        $value = floatval($formattedAmount);

        if (strpos($formattedAmount, 'k') !== false) {
            return $value * 1000;
        }

        if (strpos($formattedAmount, 'm') !== false) {
            return $value * 1_000_000;
        }

        if (strpos($formattedAmount, 'b') !== false) {
            return $value * 1_000_000_000;
        }

        return $value;
    }

    public static function getCurrencyCode(string $currencyCode): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'RUB' => '₽',
            'UAH' => '₴',
            'CHF' => 'CHF',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'NZD' => 'NZ$',
            'SGD' => 'S$',
            'HKD' => 'HK$',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
            'INR' => '₹',
            'KRW' => '₩',
            'BRL' => 'R$',
            'MXN' => 'Mex$',
            'ARS' => 'AR$',
            'CLP' => 'CL$',
            'COP' => 'COL$',
            'PEN' => 'S/',
            'UYU' => 'UY$',
            'BOB' => 'Bs.',
            'PYG' => '₲',
            'VES' => 'Bs.S',
            'CZK' => 'Kč',
            'PLN' => 'zł',
            'HUF' => 'Ft',
            'RON' => 'lei',
            'BGN' => 'лв',
            'HRK' => 'kn',
            'ISK' => 'kr',
            'TRY' => '₺',
            'GEL' => '₾',
            'MDL' => 'L',
            'BYN' => 'Br',
            'RSD' => 'дин',
            'MKD' => 'ден',
            'ALL' => 'L',
            'BAM' => 'KM',
        ];

        return $symbols[$currencyCode] ?? $currencyCode;
    }
}
