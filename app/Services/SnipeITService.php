<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SnipeITService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = AppSetting::getValue('snipeit_url');
        $this->apiKey = AppSetting::getValue('snipeit_token');
    }

    /**
     * Check if Snipe-IT is configured and enabled
     */
    public function isEnabled(): bool
    {
        return AppSetting::getValue('snipeit_enabled', '0') === '1'
            && !empty($this->baseUrl)
            && !empty($this->apiKey);
    }

    /**
     * Create an asset in Snipe-IT
     */
    public function createAsset($data)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/v1/hardware", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Sync Failed', ['body' => $response->body(), 'data' => $data]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Check if user exists by email
     */
    public function findUserByEmail($email)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/users", ['search' => $email]);

            if ($response->successful() && $response->json('total') > 0) {
                return $response->json('rows.0');
            }
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Get all hardware assets, optionally filtered by category
     */
    public function getHardware($categoryId = null, $limit = 500, $offset = 0)
    {
        $params = [
            'limit' => $limit,
            'offset' => $offset,
            'sort' => 'created_at',
            'order' => 'desc',
        ];

        if ($categoryId) {
            $params['category_id'] = $categoryId;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/hardware", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Get Hardware Failed', ['body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Get hardware assets assigned to a specific user by user ID
     */
    public function getAssetsByAssignedUserId($userId)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/users/{$userId}/assets");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Get User Assets Failed', ['user_id' => $userId, 'body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Get asset by ID
     */
    public function getAssetById($assetId)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/hardware/{$assetId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Get Asset Failed', ['asset_id' => $assetId, 'body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Check-in an asset (unassign from current user)
     */
    public function checkinAsset($assetId, $note = null)
    {
        $data = [];
        if ($note) {
            $data['note'] = $note;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/v1/hardware/{$assetId}/checkin", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Checkin Failed', ['asset_id' => $assetId, 'body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Check-out an asset to a user
     */
    public function checkoutAsset($assetId, $userId, $note = null)
    {
        $data = [
            'checkout_to_type' => 'user',
            'assigned_user' => $userId,
        ];

        if ($note) {
            $data['note'] = $note;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/v1/hardware/{$assetId}/checkout", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Checkout Failed', ['asset_id' => $assetId, 'user_id' => $userId, 'body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Get all users from Snipe-IT
     */
    public function getAllUsers($search = null, $limit = 50)
    {
        $params = [
            'limit' => $limit,
            'sort' => 'name',
            'order' => 'asc',
        ];

        if ($search) {
            $params['search'] = $search;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/users", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Get Users Failed', ['body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Get all categories from Snipe-IT
     * @param string|null $categoryType Filter by category type: asset, accessory, component, consumable, license
     */
    public function getCategories($categoryType = null)
    {
        $params = [
            'limit' => 500,
            'sort' => 'name',
            'order' => 'asc',
        ];

        if ($categoryType) {
            $params['category_type'] = $categoryType;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/categories", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Snipe-IT Get Categories Failed', ['body' => $response->body()]);
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Find user by employee number
     */
    public function findUserByEmployeeNumber($employeeNumber)
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/users", ['search' => $employeeNumber]);

            if ($response->successful() && $response->json('total') > 0) {
                $rows = $response->json('rows');
                foreach ($rows as $user) {
                    if (isset($user['employee_num']) && $user['employee_num'] === $employeeNumber) {
                        return $user;
                    }
                }
            }
        } catch (ConnectionException $e) {
            Log::error('Snipe-IT Connection Failed', ['error' => $e->getMessage()]);
        }
        return null;
    }
}
