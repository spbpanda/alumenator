<?php

namespace App\Services;

use App\Models\PnSetting;
use Crypt;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayNowIntegrationService
{
    protected string $apiKey;
    protected string $storefrontId;
    public string $baseUrl = 'https://api.paynow.gg/v1/';

    /**
     * Time in seconds that settings should be cached
     */
    public const CACHE_TTL = 60;

    /**
     * Cache key for PayNow settings
     */
    public const CACHE_KEY = 'paynow_settings';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $settings = $this->getSettings();
        $this->apiKey = $this->getDecryptedApiKey();
        $this->storefrontId = $settings->store_id ?? '';

        if (empty($this->apiKey) || empty($this->storefrontId)) {
            Cache::forget(self::CACHE_KEY);
            Cache::forget(self::CACHE_KEY . '_api_key');
        }
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
     * Get the decrypted API key.
     *
     * @return string
     */
    protected function getDecryptedApiKey(): string
    {
        return Cache::remember(self::CACHE_KEY . '_api_key', self::CACHE_TTL, function () {
            $settings = $this->getSettings();
            if ($settings && !empty($settings->api_key)) {
                try {
                    return Crypt::decryptString($settings->api_key);
                } catch (\Exception $e) {
                    return '';
                }
            }
            Log::warning('[PayNow] No API key available', ['settings_exists' => $settings ? true : false]);
            return '';
        });
    }

    /**
     * Check if the payment method is enabled.
     *
     * @return bool
     */
    public function isPaymentMethodEnabled(): bool
    {
        $settings = $this->getSettings();
        return $settings && $settings->enabled === PnSetting::STATUS_ENABLED ?? false;
    }

    /**
     * Universal request method for calling Paynow API (Management).
     *
     * @param string $httpMethod HTTP method (POST, GET, DELETE, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Data to send (for POST/PUT requests)
     * @param array $headers Custom headers
     * @return array
     */
    public function managementRequest(string $httpMethod, string $endpoint, array $data = [], array $headers = []): array
    {
        $requestHeaders = array_merge([
            'Authorization' => 'apikey ' . $this->apiKey,
            'x-paynow-store-id' => $this->storefrontId,
            'Content-Type' => 'application/json',
        ], $headers);

        if (empty($this->apiKey) || empty($this->storefrontId)) {
            return [
                'success' => false,
                'message' => 'Invalid PayNow settings: missing API key or store ID.',
            ];
        }

        return $this->performRequest($httpMethod, $endpoint, $data, $requestHeaders);
    }

    /**
     * Universal request method for calling PayNow API (Storefront).
     *
     * @param string $httpMethod HTTP method (POST, GET, DELETE, etc.)
     * @param string $endpoint API endpoint
     * @param array $data Data to send (for POST/PUT requests)
     * @param array $headers Custom headers
     * @return array
     */
    public function storefrontRequest(string $httpMethod, string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->performRequest($httpMethod, $endpoint, $data, array_merge([
            'Content-Type' => 'application/json',
            'x-paynow-store-id' => $this->storefrontId,
        ], $headers));
    }

    /**
     * Validate the request with the MineStoreCMS token.
     *
     * This method checks if the token is valid.
     *
     * @return bool True if the request is valid, false otherwise.
     */
    public function validateRequest(): bool
    {
        $licenseKey = config('app.LICENSE_KEY');

        try {
            $response = Http::timeout(2)->get("https://minestorecms.com/api/misc/{$licenseKey}/validate");

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'success') {
                    return true;
                }
            }

            Log::error('PayNow validation failed with response: ' . $response->body());
            return false;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return true;
        } catch (\Throwable $e) {
            Log::error('PayNow validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform a request to the PayNow API.
     *
     * This method checks if the PayNow payment method is enabled before proceeding
     * to send the request to the specified API endpoint.
     *
     * @param string $httpMethod The HTTP method to use (e.g., GET, POST, PUT, DELETE).
     * @param string $endpoint The API endpoint to send the request to.
     * @param array $data The data to include in the request (for POST/PUT requests).
     * @param array $headers The headers to include in the request.
     * @return array The response from the PayNow API, or an error message if the payment method is disabled.
     */
    private function performRequest(string $httpMethod, string $endpoint, array $data, array $headers): array
    {
        if (!$this->isPaymentMethodEnabled()) {
            return [
                'message' => 'PayNow is disabled.',
                'success' => false,
            ];
        }

        return $this->sendRequest($httpMethod, $endpoint, $data, $headers);
    }

    /**
     * Internal method to send HTTP requests to PayNow API.
     *
     * @param string $httpMethod HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Data to send
     * @param array $headers Headers for the request
     * @return array
     */
    protected function sendRequest(string $httpMethod, string $endpoint, array $data, array $headers): array
    {
        try {
            $url = $this->baseUrl . ltrim($endpoint, '/');
            $request = Http::withHeaders($headers);

            $response = $request->$httpMethod($url, $data);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            throw new Exception('[PayNow] API request failed: ' . $response->body());
        } catch (Exception $e) {
            Log::error("[PayNow] API Request Failed ({$endpoint}): " . $e->getMessage());

            return [
                'message' => '[PayNow] API Request Failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
