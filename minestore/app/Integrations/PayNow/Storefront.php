<?php

namespace App\Integrations\PayNow;

use App\Models\PnSetting;
use App\Models\PnVatRate;
use App\Models\Tax;
use App\Services\PayNowIntegrationService;
use Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Storefront
{
    /**
     * PayNowIntegrationService instance.
     *
     * @var PaynowIntegrationService
     */
    protected PayNowIntegrationService $paynowService;
    protected $taxMode;

    /**
     * Time in seconds that settings should be cached
     */
    public const CACHE_TTL = 60;

    /**
     * Cache key for PayNow settings
     */
    public const CACHE_KEY = 'paynow_settings';

    public function __construct(PaynowIntegrationService $paynowService)
    {
        $this->paynowService = $paynowService;
        $this->taxMode = $this->getSettings()->tax_mode ?? Tax::EXCLUSIVE;
    }

    /**
     * Get PayNow settings from cache or database
     *
     * @return \App\Models\PnSetting|null
     */
    protected function getSettings(): ?PnSetting
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return PnSetting::first();
        });
    }

    /**
     * Calculate the price for an item based on user location and discount.
     *
     * @param object $item The item object with price information
     * @param false|string $ip The IP address of the user, or false if unavailable
     * @return float The calculated visual price
     */
    public function getVisualPrice(object $item, false|string|null $ip = null): float
    {
        $ip = $ip ?? '127.0.0.1';

        if ($item->is_virtual_currency_only == 1) {
            $price = $item->virtual_price;
        } else {
            $basePrice = $item->price;

            // Step 1: Apply discount (before VAT)
            if ($item->discount > 0) {
                $discount = $basePrice * ($item->discount / 100);
                $basePrice -= $discount;
            }

            // Step 2: Apply VAT if tax is exclusive
            if (
                $this->paynowService->isPaymentMethodEnabled() &&
                $ip !== false &&
                $basePrice > 0 &&
                (int)$this->taxMode === Tax::EXCLUSIVE
            ) {
                $basePrice = $this->applyTaxByLocation($basePrice, $ip);
            }

            $price = $basePrice;
        }

        return round($price, 2);
    }

    /**
     * Apply VAT to price based on user location and handle discounts.
     *
     * @param float $price
     * @param false|string $ip The IP address to determine location, or false if unavailable
     * @return float The array with updated price including tax
     */
    public function applyVatToValue(float $price, false|string|null $ip = null): float
    {
        $ip = $ip ?? '127.0.0.1';

        if ((int)$this->taxMode === Tax::EXCLUSIVE && $ip !== false && $price > 0 && $this->paynowService->isPaymentMethodEnabled()) {
            $price = $this->applyTaxByLocation($price, $ip);
        }

        return round($price, 2);
    }

    /**
     * Apply tax to price based on user location.
     *
     * @param float $price The base price
     * @param string $ip The IP address to determine location
     * @return float Price with tax applied
     */
    private function applyTaxByLocation(float $price, string $ip): float
    {
        try {
            $geoReader = new \GeoIp2\Database\Reader(base_path('GeoLite2-Country.mmdb'));
            $countryCode = $geoReader->country($ip)->country->isoCode;

            $taxes = PnVatRate::where('country_code', $countryCode)->first();

            if (!empty($taxes)) {
                $tax = $price * ($taxes->vat_rate / 100);
                $price = $price + $tax;
            }
        } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
            Log::error('[PayNow] GeoIP address not found: ' . $e->getMessage(), [
                'ip' => $ip,
                'country_code' => $countryCode ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('[PayNow] Error applying tax by location: ' . $e->getMessage(), [
                'ip' => $ip,
                'country_code' => $countryCode ?? null,
            ]);
        }

        return $price;
    }

    /**
     * Get VAT rates for taxes from MineStoreCMS API.
     *
     * @return bool
     */
    public function syncTaxRates(): bool
    {
        $licenseKey = config('app.LICENSE_KEY');
        $apiUrl = "https://minestorecms.com/api/misc/{$licenseKey}/vat_rates";

        try {
            $response = Http::get($apiUrl);

            if (!$response->successful()) {
                Log::error('Failed to fetch VAT rates', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            $taxes = $response->json();
            if (empty($taxes) || !is_array($taxes)) {
                Log::warning('No valid VAT rates received from API', [
                    'response' => $response->body(),
                ]);
                return false;
            }

            foreach ($taxes as $tax) {
                if (!isset($tax['country_name']) || !isset($tax['standard_rate'])) {
                    Log::warning('Skipping invalid VAT rate entry', [
                        'entry' => $tax,
                    ]);
                    continue;
                }

                $countryCode = $tax['country_code'];
                $vatRate = PnVatRate::updateOrCreate(
                    ['country_code' => $countryCode],
                    [
                        'country_name' => $tax['country_name'],
                        'vat_rate' => $tax['standard_rate'],
                    ]
                );

                $vatRate->touch();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing VAT rates: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return false;
        }
    }
}
