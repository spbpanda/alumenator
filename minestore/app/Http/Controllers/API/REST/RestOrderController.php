<?php

namespace App\Http\Controllers\API\REST;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\Cart;
use App\Models\Setting;
use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RestOrderController extends Controller
{
    /**
     * Handles the creation of a cart item and payment with token-based authorization.
     *
     * @param string $api_key The API key to authorize the request.
     * @param Request $request The incoming request containing username and item ID.
     * @return \Illuminate\Http\JsonResponse The JSON response with success or error details.
     */
    public function create(string $api_key, Request $request)
    {
        $username = $request->input('username');
        $item_id = $request->input('item_id');
        $settings = Setting::select(['currency', 'virtual_currency', 'details', 'api_secret'])->find(1);
        if ($settings->api_secret !== $api_key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
            ]);
        }

        $is_virtual_currency = $request->input('is_virtual_currency', false);
        $paymentMethod = $request->input('payment_method', 'Virtual Currency');

        $currency = $is_virtual_currency ? $settings->virtual_currency : $settings->currency;

        $auth = $this->authByUsername($username);
        if (!$auth) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to make a purchase.',
            ]);
        }

        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ]);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Deactivating all active carts for the user
        Cart::where('user_id', $user->id)
            ->where('is_active', 1)
            ->update(['is_active' => 0]);

        // Adding the item to the cart
        $addItemResponse = app()->call('App\\Http\\Controllers\\CartController@addItem', [
            'id' => $item_id,
            'promoted' => 0,
            'payment_type' => 0,
            'request' => $request,
        ]);

        if (isset($addItemResponse['error'])) {
            return response()->json([
                'success' => false,
                'message' => $addItemResponse['message'],
            ]);
        } elseif (isset($addItemResponse['success']) && $addItemResponse['success'] === false) {
            return response()->json($addItemResponse);
        }

        if ($settings->details == 1) {
            $details = [
                'fullname' => 'In-Game Purchase',
                'email' => 'in-game-purchase@yourstore.com',
                'address1' => 'House 1',
                'address2' => 'Street 1',
                'city' => 'Mystery Chunk',
                'state' => 'Minecraft Server',
                'region' => 'Server World',
                'country' => 'The United Server States',
                'zipcode' => '12345'
            ];
        }

        // Adding the details to the request
        $request->merge([
            'details' => $details ?? [],
            'termsAndConditions' => true,
            'privacyPolicy' => true,
            'paymentMethod' => $paymentMethod,
            'currency' => $currency,
        ]);

        // Removing the username and item ID from the request from query
        $request->query->remove('username');
        $request->query->remove('item_id');

        // Creating a payment request
        $createPaymentResponse = app()->call('App\\Http\\Controllers\\PaymentController@create', [
            'request' => $request,
        ]);

        if (isset($createPaymentResponse['error'])) {
            return response()->json([
                'success' => false,
                'message' => $createPaymentResponse['message'],
            ]);
        } elseif (isset($createPaymentResponse['success']) && $createPaymentResponse['success'] === false) {
            return response()->json($createPaymentResponse);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase completed successfully.',
            'cart_response' => $addItemResponse,
            'payment_response' => $createPaymentResponse,
        ]);
    }

    private function authByUsername($username): bool
    {
        $uuid = null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://minestorecms.com/api/uuid/name/' . $username);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $uuid_json = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            Log::error('Authorization cURL error: ' . curl_error($ch));
        } elseif ($httpcode !== 200) {
            Log::error('HTTP error: ' . $httpcode . ' while fetching UUID for username: ' . $username);
        } else {
            $uuid_temp = json_decode($uuid_json, true);

            if (isset($uuid_temp['uuid'])) {
                $uuid = $uuid_temp['uuid'];
            } else {
                Log::error('UUID not found in the response for username: ' . $username);
            }
        }

        curl_close($ch);

        $bannedUserQuery = Ban::query()->where('username', $username);
        if (!is_null($uuid)) {
            $bannedUserQuery->orWhere('uuid', $uuid);
        }
        $bannedUser = $bannedUserQuery->first();

        if ($bannedUser) {
            $isWhitelisted = Whitelist::where('username', $username)->exists();

            if (!$isWhitelisted) {
                return false;
            }
        }

        $user = User::firstOrCreate(
            ['identificator' => $username, 'system' => 'minecraft'],
            [
                'username' => $username,
                'avatar' => 'https://mc-heads.net/body/' . $username . '/150px',
                'uuid' => $uuid,
                'api_token' => Str::random(60),
            ]
        );

        if (!$user->uuid && $uuid) {
            $user->update(['uuid' => $uuid]);
        }

        return true;
    }

    /**
     * Adds an item to the cart.
     *
     * @param int $id The ID of the item to add.
     * @param int $promoted Whether the item is promoted (default: 0).
     * @param int $paymentType The type of payment (default: 0).
     * @return array The response JSON or error details.
     */
    private function addToCart($id, $promoted = 0, $paymentType = 0)
    {
        $url = url('/api/cart/add/' . $id);
        $data = [
            'promoted' => $promoted,
            'payment_type' => $paymentType,
        ];

        return $this->sendHttpRequest($url, $data, 'POST');
    }

    /**
     * Creates a payment request.
     *
     * @param int $paymentType The type of payment to create.
     * @return array The response JSON or error details.
     */
    private function createPayment($paymentType)
    {
        $url = url('/api/payments/create');
        $data = [
            'payment_type' => $paymentType,
        ];

        return $this->sendHttpRequest($url, $data, 'POST');
    }

    /**
     * Sends an HTTP request with customizable parameters.
     *
     * @param string $url The endpoint URL.
     * @param array $data The data to include in the request body or query string.
     * @param string $method The HTTP method (default: POST).
     * @param array $headers Optional headers for the request.
     * @return array The response JSON or error details.
     */
    private function sendHttpRequest($url, $data = [], $method = 'POST', $headers = [])
    {
        try {
            // Use dynamic timeout and ensure headers are included
            $client = Http::timeout(3)->withHeaders($headers);

            // Build the request based on the HTTP method
            $response = match (strtoupper($method)) {
                'POST' => $client->post($url, $data),
                'GET' => $client->get($url, $data),
                'PUT' => $client->put($url, $data),
                'DELETE' => $client->delete($url, $data),
                default => throw new \Exception('Invalid HTTP method'),
            };

            // Handle successful response
            if ($response->successful()) {
                return $response->json();
            }

            // Log and return error on failure
            Log::error("HTTP Request Error", [
                'url' => $url,
                'method' => $method,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => true,
                'message' => 'Request failed. Status: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            // Log exception
            Log::error("Exception during HTTP Request", [
                'url' => $url,
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            return [
                'error' => true,
                'message' => 'An exception occurred: ' . $e->getMessage(),
            ];
        }
    }

    private function checkRouteError($url, $headers = [])
    {
        try {
            // Set up the HTTP client with a 3-second timeout and headers
            $client = Http::timeout(3)->withHeaders(array_merge([
                'Authorization' => 'Bearer rnNYPcoPlbrsrFUR3dTVqHsBrai3z5q5dXsAU2uRfUoj1TPDcEayKEDLI28S',
            ], $headers));

            // Send the request
            $response = $client->post($url);

            // Return the response if successful
            if ($response->successful()) {
                Log::info('Route check successful', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            // Log and return the error for non-successful responses
            Log::error('Route check failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'status' => $response->status(),
                'message' => $response->body(),
            ];
        } catch (\Exception $e) {
            // Log and return exceptions
            Log::error('Exception during route check', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }
}
