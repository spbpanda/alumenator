<?php

namespace App\Integrations\Tebex;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class Migration
{
    /**
     * The base URL for the Tebex API.
     */
    private const TEBEX_API_URL = 'https://headless.tebex.io/api/';
    private const TEBEX_PLUGIN_API_URL = 'https://plugin.tebex.io/';

    /**
     * Retrieves categories from the Tebex API.
     *
     * @param string $identifier The account identifier for fetching categories.
     * @return array The category data or an empty data array on error.
     * @throws \Exception If the API response cannot be parsed.
     */
    public function getCategories($identifier): array
    {
        $method = 'GET';
        $route = 'accounts';
        $path = 'categories?includePackages=0';

        $data = [];

        $response = $this->TebexRequest($method, $route, $identifier, $path, $data);

        Log::debug('TebexRequest response type for categories', [
            'identifier' => $identifier,
            'type' => get_class($response)
        ]);

        if ($response->status() !== 200) {
            Log::error('Failed to fetch categories from Tebex API', [
                'identifier' => $identifier,
                'status' => $response->status(),
                'error' => method_exists($response, 'body') ? $response->body() : $response->getContent()
            ]);
            return ['data' => []];
        }

        try {
            $categories = $response instanceof JsonResponse
                ? $response->getData(true)
                : $response->json();

            Log::info('Successfully fetched categories from Tebex API', [
                'identifier' => $identifier,
                'category_count' => count($categories['data'] ?? [])
            ]);

            return $categories;
        } catch (\Exception $e) {
            Log::error('Failed to parse Tebex API response for categories', [
                'identifier' => $identifier,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Retrieves packages from the Tebex API.
     *
     * @param string $identifier The account identifier for fetching packages.
     * @return array The package data or an empty data array on error.
     * @throws \Exception If the API response cannot be parsed.
     */
    public function getPackages($identifier): array
    {
        $method = 'GET';
        $route = 'accounts';
        $path = 'packages';

        $data = [];

        $response = $this->TebexRequest($method, $route, $identifier, $path, $data);

        Log::debug('TebexRequest response type for packages', [
            'identifier' => $identifier,
            'type' => get_class($response)
        ]);

        if ($response->status() !== 200) {
            Log::error('Failed to fetch packages from Tebex API', [
                'identifier' => $identifier,
                'status' => $response->status(),
                'error' => method_exists($response, 'body') ? $response->body() : $response->getContent()
            ]);
            return ['data' => []];
        }

        try {
            $packages = $response instanceof JsonResponse
                ? $response->getData(true)
                : $response->json();

            Log::info('Successfully fetched packages from Tebex API', [
                'identifier' => $identifier,
                'package_count' => count($packages['data'] ?? [])
            ]);

            return $packages;
        } catch (\Exception $e) {
            Log::error('Failed to parse Tebex API response for packages', [
                'identifier' => $identifier,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Retrieves paginated payment data from the Tebex Plugin API.
     *
     * @param string $secret The secret key for authenticating with the Tebex Plugin API.
     * @param int $page The page number to retrieve (default is 1).
     * @return array The payment data or an empty data array on error.
     * @throws \Exception If the API response cannot be parsed.
     */
    public function getPaymentsPaginated($secret, $page = 1): array
    {
        $method = 'GET';
        $route = 'payments?paged=' . $page;
        $data = [];

        $response = $this->TebexPluginRequest($method, $route, $data, $secret);

        Log::debug('TebexPluginRequest response type for payments', [
            'page' => $page,
            'type' => get_class($response)
        ]);

        if ($response->status() !== 200) {
            Log::error('Failed to fetch payments from Tebex Plugin API', [
                'page' => $page,
                'status' => $response->status(),
                'error' => method_exists($response, 'body') ? $response->body() : $response->getContent()
            ]);
            return ['data' => []];
        }

        try {
            $payments = $response instanceof JsonResponse
                ? $response->getData(true)
                : $response->json();

            Log::info('Successfully fetched payments from Tebex Plugin API', [
                'page' => $page,
                'payment_count' => count($payments['data'] ?? [])
            ]);

            return $payments;
        } catch (\Exception $e) {
            Log::error('Failed to parse Tebex Plugin API response for payments', [
                'page' => $page,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sends a request to the Tebex API and returns the response.
     *
     * @param string $method The HTTP method to use for the request (e.g., 'GET', 'POST').
     * @param string $route The route of the API to send the request to.
     * @param string $identifier The identifier for the request.
     * @param string $path The path for the request.
     * @param array $data The data to send with the request, if any.
     *
     * @return JsonResponse The response from the Tebex API.
     *
     * @call $response = $tebexController->request($method, $route, $identifier, $path, $data);
     */
    public function TebexRequest(string $method, string $route, string $identifier, string $path, array $data = []): JsonResponse
    {
        $url = self::TEBEX_API_URL . $route . '/' . $identifier . '/' . $path;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'CF-IPCountry: DE',
            ],
        ]);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        try {
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);

            curl_close($curl);

            if ($curlError) {
                Log::error("Tebex Request Failed (" . $route . '/' . $identifier . '/' . $path . ") >>> " . $curlError);
                return response()->json([
                    'message' => 'Tebex API Request Failed',
                    'error' => $curlError,
                ], 500);
            }

            $responseData = json_decode($response, true);

            if ($httpCode == 200) {
                return response()->json($responseData, 200);
            }

            Log::error("Tebex Request Failed (" . $route . '/' . $identifier . '/' . $path . ") >>> " . $response);
            return response()->json([
                'message' => 'Tebex API Request Failed',
                'error' => $responseData,
            ], $httpCode);

        } catch (\Exception $e) {
            curl_close($curl);
            Log::error('Tebex API Request Failed >>> ' . $e->getMessage());
            return response()->json([
                'message' => 'Tebex API Request Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sends a request to the Tebex Plugin API and returns the response.
     *
     * @param string $method The HTTP method to use for the request (e.g., 'GET', 'POST').
     * @param string $route The route of the API to send the request to.
     * @param array $data The data to send with the request, if any.
     * @param string $pluginSecret The secret key for the Tebex Plugin API.
     *
     * @return JsonResponse The response from the Tebex Plugin API.
     *
     * @call $response = $tebexController->request($method, $route, $data, $pluginSecret);
     */
    public function TebexPluginRequest(string $method, string $route, array $data = [], string $pluginSecret): JsonResponse
    {
        $url = self::TEBEX_PLUGIN_API_URL . $route;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => [
                "X-Tebex-Secret: " . $pluginSecret,
                "Content-Type: application/json",
                "CF-IPCountry: DE",
            ],
        ]);

        if (!empty($data) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        try {
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);

            curl_close($curl);

            if ($curlError) {
                Log::error('Tebex Plugin API Request Failed >>> ' . $curlError);
                return response()->json([
                    'message' => 'Tebex Plugin API Request Failed (cURL Error)',
                    'error' => $curlError,
                ], 500);
            }

            $responseData = json_decode($response, true);

            return response()->json($responseData, $httpCode);

        } catch (\Exception $e) {
            curl_close($curl);
            Log::error('Tebex Plugin API Request Failed >>> ' . $e->getMessage());
            return response()->json([
                'message' => 'Tebex Plugin API Request Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
