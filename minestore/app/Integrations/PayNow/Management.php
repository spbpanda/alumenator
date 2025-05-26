<?php

namespace App\Integrations\PayNow;

use App\Models\Item;
use App\Models\PnProductReference;
use App\Models\PnSetting;
use App\Models\PnVariableReference;
use App\Models\Tax;
use App\Services\PayNowIntegrationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Management
{
    protected string $storefrontId;
    protected PayNowIntegrationService $paynowService;

    /**
     * Time in seconds that settings should be cached
     */
    public const CACHE_TTL = 60;

    /**
     * Cache key for PayNow settings
     */
    public const CACHE_KEY = 'paynow_settings';

    /**
     * Initialize the PayNow Management integration.
     *
     * @param PayNowIntegrationService $paynowService
     */
    public function __construct(PayNowIntegrationService $paynowService)
    {
        $this->paynowService = $paynowService;
        $this->storefrontId = $this->getSettings()->store_id ?? '';
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
     * Purges the PayNow cache.
     *
     * This static method clears the cached PayNow settings and API key
     * by removing the associated cache keys. If the operation is successful,
     * it returns true. If an exception occurs, it logs the error and returns false.
     *
     * @return bool True if the cache was successfully purged, false otherwise.
     */
    public static function purgeCache(): bool
    {
        try {
            $cacheKey = self::CACHE_KEY;
            $apiKey = self::CACHE_KEY . '_api_key';
            Cache::forget($cacheKey);
            Cache::forget($apiKey);
            return true;
        } catch (\Exception $e) {
            Log::error('[PayNow] Failed to purge cache.', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get the tax mode from PayNow payment settings.
     *
     * @return ?int Tax::INCLUSIVE, Tax::EXCLUSIVE, or null on failure
     */
    public function getTaxMode(): ?int
    {
        $endpoint = "stores/{$this->storefrontId}/payments/settings";
        $result = $this->makeRequest('get', $endpoint, [], 'Fetch PayNow payment settings');

        if ($result === null) {
            return null;
        }

        return $result['store_tax_inclusive_pricing'] ? Tax::INCLUSIVE : Tax::EXCLUSIVE;
    }

    /**
     * Retrieves the store settings from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch the store settings
     * for the current storefront. If the request is successful, the store data is
     * returned. Otherwise, null is returned.
     *
     * @return array|null The store settings data, or null if the request fails.
     */
    public function getStore(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}";
        $result = $this->makeRequest('get', $endpoint, [], 'Fetch PayNow store settings');

        return $result ?? null;
    }

    /**
     * Update Store Object with PayNow settings.
     *
     * @param array $data
     * @return true|null
     */
    public function updateStoreSettings(array $data): ?true
    {
        $endpoint = "stores/{$this->storefrontId}";
        $result = $this->makeRequest('patch', $endpoint, $data, 'Updating PayNow store settings');

        // Case 1: Error with "no changes to apply" - bypass the error and return true
        if (
            isset($result['status'], $result['message']) &&
            $result['status'] === 400 &&
            $result['message'] === 'no changes to apply'
        ) {
            return true;
        }

        // Case 2: Success response - return true
        if (isset($result['id'])) {
            return true;
        }

        Log::error('[PayNow] Store update failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Creates a new product in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a product
     * using the provided data. If the product is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the product.
     * @return array|null The created product data, or null if the creation fails.
     */
    public function createProduct(array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/products";
        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow product');
        Log::info('Create PayNow Product: ' . json_encode($result));

        if (!empty($result['id'])) {
            Log::info('[PayNow] Product created successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Product creation failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Updates an existing product in the PayNow store.
     *
     * This method sends a PATCH request to the PayNow API to update a product
     * using the provided data. If the product is successfully updated, true
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $productId The ID of the product to update.
     * @param array $data The data to update the product with.
     * @return array|null The updated product data, or null if the update fails.
     */
    public function updateProduct(string $productId, array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$productId}";
        $result = $this->makeRequest('patch', $endpoint, $data, 'Updating PayNow product');

        if (isset($result['id'])) {
            Log::info('[PayNow] Product updated successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Product update failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Deletes a product from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a product
     * using the provided PnProductReference object. If the deletion is successful,
     * true is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param PnProductReference $pnProductReference The PnProductReference object containing the product ID.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteProduct(PnProductReference $pnProductReference): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$pnProductReference->external_package_id}";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow product');

        return true;
    }

    /**
     * Deletes a product from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a product
     * using the provided PnProductReference object. If the deletion is successful,
     * true is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $pnProductID
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deletePayNowProduct(string $pnProductID): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$pnProductID}";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow product');

        return true;
    }

    /**
     * Retrieves a product from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch a product
     * using the provided product ID. If the product is successfully retrieved,
     * its data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $pnProductID The ID of the product to retrieve.
     * @return array|null The product data, or null if retrieval fails.
     */
    public function getProduct(string $pnProductID): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$pnProductID}";
        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow product');

        if (isset($result['id'])) {
            Log::info('[PayNow] Product retrieved successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Product retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $pnProductID,
        ]);

        return null;
    }

    /**
     * Retrieves all products from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch all products
     * in the store. If the products are successfully retrieved, an array of
     * product data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @return array|null The array of product data, or null if retrieval fails.
     */
    public function getProducts(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/products";
        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow products');

        if (is_array($result)) {
            return $result;
        }

        Log::error('[PayNow] Products retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => null,
        ]);

        return null;
    }

    /**
     * Checks if a product has an image URL.
     *
     * This method checks if the provided product array contains a non-empty
     * 'image_url' field. If it does, true is returned; otherwise, false is returned.
     *
     * @param PnProductReference $pnProduct The product array to check.
     * @return bool True if the product has an image URL, false otherwise.
     */
    public function checkProductImage(PnProductReference $pnProduct): bool
    {
        $result = $this->getProduct($pnProduct->external_package_id);

        if (!empty($result['image_url'])) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a variable (product) from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a variable (product)
     * using the provided PnVariableReference object. If the deletion is successful,
     * true is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param PnVariableReference $pnVariableReference The PnProductReference object containing the product ID.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteVariableProduct(PnVariableReference $pnVariableReference): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$pnVariableReference->external_product_id}";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow variable (product)');

        return true;
    }

    /**
     * Creates a new tag in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a tag
     * using the provided data. If the tag is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the tag.
     * @return string|null The ID of the created tag, or null if the creation fails.
     */
    public function createTag(array $data)
    {
        $endpoint = "stores/{$this->storefrontId}/tags";
        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow tag');
        Log::info('Create PayNow Tag: ' . json_encode($result));

        if (!empty($result['id'])) {
            Log::info('[PayNow] Tag created successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'data' => $result,
            ]);

            return $result['id'];
        }

        Log::error('[PayNow] Tag creation failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Updates an existing tag in the PayNow store.
     *
     * This method sends a PATCH request to the PayNow API to update a tag
     * using the provided data. If the tag is successfully updated, true
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data to update the tag with.
     * @param string $tagId The ID of the tag to update.
     * @return true|null True if the update was successful, null otherwise.
     */
    public function updateTag(string $tagId, array $data)
    {
        $endpoint = "stores/{$this->storefrontId}/tags/{$tagId}";
        $result = $this->makeRequest('patch', $endpoint, $data, 'Updating PayNow tag');

        if (isset($result['id'])) {
            Log::info('[PayNow] Tag updated successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return true;
        }

        Log::error('[PayNow] Tag update failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Deletes a tag from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a tag
     * using the provided tag ID. If the deletion is successful, true is returned.
     * Otherwise, an error is logged, and null is returned.
     *
     * @param string $tagId The ID of the tag to delete.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteTag(string $tagId): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/tags/{$tagId}";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow tag');

        return true;
    }

    /**
     * Retrieves a tag from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch a tag
     * using the provided tag ID. If the tag is successfully retrieved,
     * its data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $pnTagID The ID of the tag to retrieve.
     * @return array|null The tag data, or null if retrieval fails.
     */
    public function getTag(string $pnTagID): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/tags/{$pnTagID}";
        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow tag');

        if (isset($result['id'])) {
            Log::info('[PayNow] Tag retrieved successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Tag retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $pnTagID,
        ]);

        return null;
    }

    /**
     * Retrieves all tags from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch all tags
     * in the store. If the tags are successfully retrieved, an array of
     * tag data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @return array|null The array of tag data, or null if retrieval fails.
     */
    public function getTags(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/tags";
        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow tags');

        if (is_array($result)) {
            return $result;
        }

        Log::error('[PayNow] Tags retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => null,
        ]);

        return null;
    }

    /**
     * Creates a new game server in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a game server
     * using the provided data. If the game server is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the game server.
     * @return string|null The ID of the created game server, or null if the creation fails.
     */
    public function createServer(array $data): ?string
    {
        $endpoint = "stores/{$this->storefrontId}/gameservers";

        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow game server');

        if (!empty($result['id'])) {
            Log::info('[PayNow] Game server created successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'data' => $result,
            ]);

            return $result['id'];
        }

        Log::error('[PayNow] Game server creation failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Updates an existing game server in the PayNow store.
     *
     * This method sends a PATCH request to the PayNow API to update a game server
     * using the provided data. If the game server is successfully updated, true
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $serverId The ID of the game server to update.
     * @param array $data The data to update the game server with.
     * @return true|null True if the update was successful, null otherwise.
     */
    public function updateServer(string $serverId, array $data): ?true
    {
        $endpoint = "stores/{$this->storefrontId}/gameservers/{$serverId}";

        $result = $this->makeRequest('patch', $endpoint, $data, 'Updating PayNow game server');

        if (isset($result['id'])) {
            Log::info('[PayNow] Game server updated successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return true;
        }

        Log::error('[PayNow] Game server update failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Retrieves a game server from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch a game server
     * using the provided server ID. If the game server is successfully retrieved,
     * its data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $pnServerID The ID of the game server to retrieve.
     * @return array|null The game server data, or null if retrieval fails.
     */
    public function getServer(string $pnServerID): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/gameservers/{$pnServerID}";
        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow game server');

        if (isset($result['id'])) {
            Log::info('[PayNow] Game server retrieved successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Game server retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $pnServerID,
        ]);

        return null;
    }

    /**
     * Retrieves all game servers from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch all game servers
     * in the store. If the game servers are successfully retrieved, an array of
     * game server data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @return array|null The array of game server data, or null if retrieval fails.
     */
    public function getServers(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/gameservers";
        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow game servers');

        if (is_array($result)) {
            return $result;
        }

        Log::error('[PayNow] Game servers retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => null,
        ]);

        return null;
    }

    /**
     * Uploads an image to a product in the PayNow store.
     *
     * This method uploads an image file to a product using the provided external ID.
     * If the upload is successful, true is returned. Otherwise, an error is logged,
     * and false is returned.
     *
     * @param Item $item The item to upload the image for.
     * @param string $externalId The external ID of the product.
     * @return bool True if the upload was successful, false otherwise.
     */
    public function uploadImageToProduct(Item $item, string $externalId): bool
    {
        if (!$item->image && $item->pnProductReference->exists()) {
            Log::info('[PayNow] No image to upload for product or product does not exists in references.', [
                'item_id' => $item->id,
            ]);
        }

        try {
            // Step 1: Get the upload URL
            $endpoint = "stores/{$this->storefrontId}/products/{$externalId}/image-upload-url";
            $imageUrlResponse = $this->makeRequest('get', $endpoint, [], 'Getting PayNow image upload URL');

            if (!$imageUrlResponse || (!isset($imageUrlResponse['upload_url']) && !isset($imageUrlResponse['url']))) {
                Log::error('[PayNow] ItemObserver: Failed to get image upload URL', [
                    'item_id' => $item->id,
                    'external_package_id' => $externalId,
                    'response' => $imageUrlResponse,
                ]);
                return false;
            }

            $uploadUrl = $imageUrlResponse['upload_url'] ?? $imageUrlResponse['url'];

            // Step 2: Upload the image
            $imagePath = public_path('img/items/' . $item->image);
            if (!file_exists($imagePath)) {
                Log::error('[PayNow] ItemObserver: Image file not found', [
                    'item_id' => $item->id,
                    'image_path' => $imagePath,
                ]);
                return false;
            }

            $uploadResponse = Http::attach(
                'file',
                file_get_contents($imagePath),
                $item->image,
                ['Content-Type' => $this->getMimeType($imagePath)]
            )->post($uploadUrl);

            if ($uploadResponse->failed()) {
                Log::error('[PayNow] ItemObserver: Image upload failed', [
                    'item_id' => $item->id,
                    'external_package_id' => $externalId,
                    'status' => $uploadResponse->status(),
                    'body' => $uploadResponse->body(),
                ]);
                return false;
            }

            $uploadData = $uploadResponse->json();
            $imageId = $uploadData['result']['id'] ?? null;

            if (!$imageId) {
                Log::error('[PayNow] ItemObserver: Image ID not returned after upload', [
                    'item_id' => $item->id,
                    'external_package_id' => $externalId,
                    'response' => $uploadData,
                ]);
                return false;
            }

            // Step 3: Confirm the image upload
            $endpoint = "stores/{$this->storefrontId}/products/{$externalId}/image-upload-finish";
            $this->makeRequest('post', $endpoint, ['image_id' => $imageId], 'Confirming PayNow image upload');
            Log::info('[PayNow] ItemObserver: Image uploaded and confirmed successfully.');

            // Step 4: Verify the image_url on the product
            $checkImage = $this->checkProductImage($item->pnProductReference);
            if (!$checkImage) {
                Log::error('[PayNow] ItemObserver: Image URL not found after upload', [
                    'item_id' => $item->id,
                    'external_package_id' => $externalId,
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('[PayNow] ItemObserver: Image upload exception', [
                'item_id' => $item->id,
                'external_package_id' => $externalId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Deletes a product image from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a product
     * image using the provided external ID. If the deletion is successful, true
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $externalId The external ID of the product.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteProductImage(string $externalId): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$externalId}/image";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow product image');

        return true;
    }

    /**
     * Uploads a store logo to the PayNow store.
     *
     * This method uploads a logo image to the PayNow store using the provided
     * type and image file. If the upload is successful, true is returned.
     * Otherwise, an error is logged, and false is returned.
     *
     * @param int $type The type of the logo (1 or 2).
     * @param UploadedFile $image The uploaded image file from the request.
     * @return bool True if the upload was successful, false otherwise.
     */
    public function uploadStoreLogo(int $type, UploadedFile $image): bool
    {
        $logoType = $type === 2 ? 'logo_square_url' : 'logo_url';

        $webstore = $this->getStore();
        if ($webstore && $webstore[$logoType]) {
            $this->deleteStoreLogo($type);
        }

        try {
            // Step 1: Get the upload URL
            $endpoint = "stores/{$this->storefrontId}/logo-upload-url?logo_type={$type}";

            $imageUrlResponse = $this->makeRequest('get', $endpoint, [], 'Getting PayNow logo upload URL');

            if (!$imageUrlResponse || (!isset($imageUrlResponse['upload_url']) && !isset($imageUrlResponse['url']))) {
                Log::error('[PayNow] Failed to get logo upload URL', [
                    'type' => $type,
                    'response' => $imageUrlResponse,
                ]);
                return false;
            }

            Log::error('[PayNow] Logo upload URL response', [
                'headers' => $imageUrlResponse['headers'] ?? null,
                'type' => $type,
                'response' => $imageUrlResponse,
            ]);

            // Step 2: Upload the image
            $uploadUrl = $imageUrlResponse['upload_url'];
            $uploadResponse = Http::attach(
                'file',
                file_get_contents($image),
                basename($image),
                ['Content-Type' => $this->getMimeType($image)]
            )->post($uploadUrl);

            if ($uploadResponse->failed()) {
                Log::error('[PayNow] Logo upload failed', [
                    'type' => $type,
                    'status' => $uploadResponse->status(),
                    'body' => $uploadResponse->body(),
                    'headers' => $uploadResponse->headers(),
                ]);
                return false;
            }

            $uploadData = $uploadResponse->json();
            $imageId = $uploadData['result']['id'] ?? null;
            if (!$imageId) {
                Log::error('[PayNow] Logo ID not returned after upload', [
                    'type' => $type,
                    'response' => $uploadData,
                ]);
                return false;
            }

            // Step 3: Confirm the image upload
            $endpoint = "stores/{$this->storefrontId}/logo-upload-finish";
            $this->makeRequest('post', $endpoint, ['image_id' => $imageId, 'logo_type' => $type], 'Confirming PayNow logo upload');

            Log::info('[PayNow] Logo uploaded and confirmed successfully.');

            // Step 4: Verify the logo uploaded
            $checkLogo = $this->getStore();
            if (!$checkLogo || empty($checkLogo[$logoType])) {
                Log::error('[PayNow] Logo URL not found after upload', [
                    'type' => $type,
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('[PayNow] Exception while uploading store logo.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Deletes a store logo from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a store
     * logo using the provided type. If the deletion is successful, true is returned.
     * Otherwise, an error is logged, and null is returned.
     *
     * @param int $type The type of the logo to delete (1 or 2).
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteStoreLogo(int $type): true
    {
        $endpoint = "stores/{$this->storefrontId}/logo?type={$type}";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow store logo');

        return true;
    }

    /**
     * Deletes a game server from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a game server
     * using the provided server ID. If the deletion is successful, true is returned.
     * Otherwise, an error is logged, and null is returned.
     *
     * @param string $serverId The ID of the game server to delete.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteServer(string $serverId): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/gameservers/{$serverId}";

        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow game server');

        return true;
    }

    /**
     * Retrieves the MIME type of file based on its extension.
     *
     * This method checks the file extension and returns the corresponding
     * MIME type. If the extension is not recognized, 'application/octet-stream'
     * is returned as a fallback.
     *
     * @param string $path The file path to check.
     * @return string The MIME type of the file.
     */
    private function getMimeType(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    /**
     * Retrieves all webhooks from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch all webhooks
     * in the store. If the webhooks are successfully retrieved, an array of
     * webhook data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @return array|null The array of webhook data, or null if retrieval fails.
     */
    public function getWebhooks(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/webhooks";

        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow webhooks');
        Log::info('[PayNow] Webhooks retrieved successfully.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $result,
        ]);

        if (is_array($result)) {
            return $result;
        }

        return null;
    }

    /**
     * Creates a new webhook in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a webhook
     * using the provided data. If the webhook is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the webhook.
     * @return array|null The ID of the created webhook, or null if the creation fails.
     */
    public function createWebhook(array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/webhooks";

        Log::info('[PayNow] Creating webhook.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'data' => $data,
        ]);
        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow webhook');
        Log::info('[PayNow] Webhook created successfully.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $result,
        ]);
        if (is_array($result) && !empty($result)) {
            return $result;
        }

        return null;
    }

    /**
     * Updates an existing webhook in the PayNow store.
     *
     * This method sends a PATCH request to the PayNow API to update a webhook
     * using the provided data. If the webhook is successfully updated, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $webhookId The ID of the webhook to update.
     * @param array $data The data to update the webhook with.
     * @return array|null The ID of the updated webhook, or null if the update fails.
     */
    public function updateWebhook(string $webhookId, array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/webhooks/{$webhookId}";

        $result = $this->makeRequest('patch', $endpoint, $data, 'Updating PayNow webhook');
        if (is_array($result) && !empty($result)) {
            return $result;
        }

        return null;
    }

    /**
     * Deletes a webhook from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a webhook
     * using the provided webhook ID. If the deletion is successful, true is returned.
     * Otherwise, an error is logged, and null is returned.
     *
     * @param string $webhookId The ID of the webhook to delete.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteWebhook(string $webhookId): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/webhooks/{$webhookId}";

        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow webhook');

        return true;
    }

    /**
     * Retrieves the onboarding status from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch the onboarding
     * status for the current storefront. If the request is successful and the
     * response contains a valid `store_id`, the onboarding data is returned.
     * Otherwise, an error is logged, and null is returned.
     *
     * @return array|null The onboarding status data, or null if the request fails.
     */
    public function getOnboarding(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/trust/onboarding/status";

        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow onboarding status');

        if (is_array($result) && !empty($result) && isset($result['store_id'])) {
            return $result;
        }

        Log::error('[PayNow] Onboarding retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => null,
        ]);

        return null;
    }

    /**
     * Retrieves the billing status from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch the billing
     * status for the current storefront. If the request is successful and the
     * response contains valid billing data, it is returned. Otherwise, an error
     * is logged, and null is returned.
     *
     * @return array|null The billing status data, or null if the request fails.
     */
    public function getBillingStatus(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/billing/status";

        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow billing status');
        if (is_array($result) && !empty($result)) {
            return $result;
        }

        Log::error('[PayNow] Billing status retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => null,
        ]);

        return null;
    }

    /**
     * Retrieves the alerts from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch the alerts
     * for the current storefront. If the request is successful and the
     * response contains valid alert data, it is returned. Otherwise, an error
     * is logged, and null is returned.
     *
     * @return array|null The alerts data, or null if the request fails.
     */
    public function getAlerts(): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/billing/status";

        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow alerts');
        if (is_array($result) && !empty($result) && isset($result['alerts'])) {
            return $result['alerts'] ?? [];
        }

        Log::error('[PayNow] Alerts retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => null,
        ]);

        return null;
    }

    /**
     * Retrieves an order from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch an order
     * using the provided order ID. If the order is successfully retrieved,
     * its data is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $orderId The ID of the order to retrieve.
     * @return array|null The order data, or null if retrieval fails.
     */
    public function getOrder(string $orderId): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/orders/{$orderId}";

        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow order');

        if (isset($result['id'])) {
            Log::info('[PayNow] Order retrieved successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Order retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $orderId,
        ]);

        return null;
    }

    /**
     * Refunds an order in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to refund an order
     * using the provided order ID. If the refund is successful, its data is
     * returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $orderId The ID of the order to refund.
     * @return array|null The refund data, or null if the refund fails.
     */
    public function refundOrder(string $orderId): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/orders/{$orderId}/refund";
        $data = [
            'order_line_id' => $orderId,
        ];

        $result = $this->makeRequest('post', $endpoint, $data, 'Refunding PayNow order');

        if (isset($result['id'])) {
            Log::info('[PayNow] Order refunded successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Order refund failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $orderId,
        ]);

        return null;
    }

    /**
     * Retrieves a subscription from the PayNow store.
     *
     * This method sends a GET request to the PayNow API to fetch a subscription
     * using the provided subscription ID. If the subscription is successfully
     * retrieved, its data is returned. Otherwise, an error is logged, and null
     * is returned.
     *
     * @param string $subscriptionId The ID of the subscription to retrieve.
     * @return array|null The subscription data, or null if retrieval fails.
     */
    public function getSubscription(string $subscriptionId): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/subscriptions/{$subscriptionId}";

        $result = $this->makeRequest('get', $endpoint, [], 'Getting PayNow subscription');

        if (isset($result['id'])) {
            Log::info('[PayNow] Subscription retrieved successfully.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $result,
            ]);

            return $result;
        }

        Log::error('[PayNow] Subscription retrieval failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $subscriptionId,
        ]);

        return null;
    }

    /**
     * Cancels a subscription in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to cancel a subscription
     * using the provided subscription ID. If the cancellation is successful, true
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $subscriptionId The ID of the subscription to cancel.
     * @return bool True if the cancellation was successful, false otherwise.
     */
    public function cancelSubscription(string $subscriptionId): true
    {
        $endpoint = "stores/{$this->storefrontId}/subscriptions/{$subscriptionId}/cancel";

        $result = $this->makeRequest('post', $endpoint, [], 'Cancelling PayNow subscription');

        Log::info('[PayNow] Subscription cancelled successfully.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $result,
        ]);

        return true;
    }

    /**
     * Creates a new customer in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a customer
     * using the provided data. If the customer is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the customer.
     * @return array|null The ID of the created customer, or null if the creation fails.
     */
    public function createCustomer(array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/customers";

        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow customer');
        Log::info('Create paynow customer: ' . json_encode($result));
        if (isset($result['id'])) {
            return $result;
        }

        Log::error('[PayNow] Customer creation failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Updates an existing customer in the PayNow store.
     *
     * This method sends a PATCH request to the PayNow API to update a customer
     * using the provided customer ID and data. If the update is successful, true
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param string $customerId The ID of the customer to update.
     * @param array $data The data to update the customer with.
     * @return array|null The updated customer data, or null if the update fails.
     */
    public function updateCustomer(string $customerId, array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/customers/{$customerId}";

        $result = $this->makeRequest('patch', $endpoint, $data, 'Updating PayNow customer');
        if (isset($result['id'])) {
            return $result;
        }

        Log::error('[PayNow] Customer update failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Deletes a product from the PayNow store.
     *
     * This method sends a DELETE request to the PayNow API to remove a product
     * using the provided product ID. If the deletion is successful, true is returned.
     * Otherwise, an error is logged, and null is returned.
     *
     * @param string $productId The ID of the product to delete.
     * @return bool|null True if the deletion was successful, null otherwise.
     */
    public function deleteRemainProduct(string $productId): ?bool
    {
        $endpoint = "stores/{$this->storefrontId}/products/{$productId}";
        $this->makeRequest('delete', $endpoint, [], 'Deleting PayNow product');

        return true;
    }

    /**
     * Creates a new coupon in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a coupon
     * using the provided data. If the coupon is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the coupon.
     * @return array|null The ID of the created coupon, or null if the creation fails.
     */
    public function createCoupon(array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/coupons";

        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow coupon');
        if (!isset($result['id'])) {
            Log::error('[PayNow] Coupon creation failed due to unexpected HTTP response code.', [
                'url' => "{$this->paynowService->baseUrl}{$endpoint}",
                'response_code' => http_response_code(),
                'data' => $data,
            ]);

            return null;
        }

        return $result;
    }

    /**
     * Creates a new checkout in the PayNow store.
     *
     * This method sends a POST request to the PayNow API to create a checkout
     * using the provided data. If the checkout is successfully created, its ID
     * is returned. Otherwise, an error is logged, and null is returned.
     *
     * @param array $data The data required to create the checkout.
     * @return array|null The ID of the created checkout, or null if the creation fails.
     */
    public function createCheckout(array $data): ?array
    {
        $endpoint = "stores/{$this->storefrontId}/checkouts";

        $result = $this->makeRequest('post', $endpoint, $data, 'Creating PayNow checkout');
        if (isset($result['id'])) {
            return $result;
        }

        Log::error('[PayNow] Checkout creation failed due to unexpected HTTP response code.', [
            'url' => "{$this->paynowService->baseUrl}{$endpoint}",
            'response_code' => http_response_code(),
            'data' => $data,
        ]);

        return null;
    }

    /**
     * Base method for making PayNow API requests with error handling.
     *
     * @param string $method HTTP method (get, post, patch, etc.)
     * @param string $endpoint API endpoint path
     * @param array $data Optional data to send with the request
     * @param string $errorContext Context description for error logging
     * @return array|null Response data or null on failure
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], string $errorContext = 'API request'): ?array
    {
        $url = "{$this->paynowService->baseUrl}{$endpoint}";

        try {
            $result = $this->paynowService->managementRequest($method, $endpoint, $data);

            if (isset($result['error'])) {
                $raw = str_replace('[PayNow] API request failed: ', '', $result['error']);
                $decoded = json_decode($raw, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    Log::warning("[PayNow] API responded with an error.", [
                        'url' => $url,
                        'status' => $decoded['status'] ?? null,
                        'code' => $decoded['code'] ?? null,
                        'message' => $decoded['message'] ?? null,
                    ]);

                    return $decoded + ['message' => $decoded['message'] ?? null];
                }

                Log::error("[PayNow] Failed to {$errorContext}.", [
                    'url' => $url,
                    'error' => $result['error'],
                    'data' => !empty($data) ? $data : null,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("[PayNow] Exception while {$errorContext}.", [
                'message' => $e->getMessage(),
                'url' => $url,
                'data' => !empty($data) ? $data : null,
            ]);

            return null;
        }
    }
}
