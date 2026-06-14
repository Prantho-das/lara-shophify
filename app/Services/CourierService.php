<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourierService
{
    public static function parseShippingDetails($shippingAddress)
    {
        $lines = explode("\n", $shippingAddress);
        $details = [
            'name' => '',
            'phone' => '',
            'address' => '',
        ];
        
        $addressLines = [];
        $captureAddress = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'Name:')) {
                $details['name'] = trim(substr($line, 5));
            } elseif (str_starts_with($line, 'Phone:')) {
                $details['phone'] = trim(substr($line, 6));
            } elseif (str_starts_with($line, 'Address:')) {
                $details['address'] = trim(substr($line, 8));
                $captureAddress = true;
            } elseif (str_starts_with($line, 'Country:') || str_starts_with($line, 'District:') || str_starts_with($line, 'Area:')) {
                $addressLines[] = $line;
            } elseif (str_starts_with($line, 'Notes:')) {
                $captureAddress = false;
            } elseif ($captureAddress) {
                $addressLines[] = $line;
            }
        }
        
        if (!empty($addressLines)) {
            $details['address'] = $details['address'] ? $details['address'] . ', ' . implode(', ', $addressLines) : implode(', ', $addressLines);
        }
        
        if (empty($details['address'])) {
            $details['address'] = $shippingAddress;
        }

        if (empty($details['name'])) {
            $details['name'] = 'Customer';
        }

        if (empty($details['phone'])) {
            $details['phone'] = '01700000000';
        }
        
        return $details;
    }

    public static function sendToSteadfast($order, $codAmount, $note = '')
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $apiKey = $settings['steadfast_api_key'] ?? '';
        $secretKey = $settings['steadfast_secret_key'] ?? '';

        $details = self::parseShippingDetails($order->shipping_address);
        
        // Check if API keys exist
        if (empty($apiKey) || empty($secretKey)) {
            Log::info("Steadfast API key or Secret key missing. Falling back to Mock consignment creation.");
            return [
                'success' => true,
                'tracking_code' => 'MOCK-STDF-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                'status' => 'mocked',
                'message' => 'Consignment created successfully (MOCK MODE - Credentials missing)'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Api-Key' => $apiKey,
                'Secret-Key' => $secretKey,
                'Content-Type' => 'application/json'
            ])->post('https://portal.steadfast.com.bd/api/v1/create_order', [
                'invoice_id' => 'ORDER-' . $order->id,
                'recipient_name' => $details['name'],
                'recipient_phone' => $details['phone'],
                'recipient_address' => $details['address'],
                'cod_amount' => (float)$codAmount,
                'note' => $note ?: 'Delivering order #' . $order->id
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? 0) === 200 && isset($data['order_info']['tracking_code'])) {
                    return [
                        'success' => true,
                        'tracking_code' => $data['order_info']['tracking_code'],
                        'status' => $data['order_info']['status'] ?? 'in_review',
                        'message' => $data['message'] ?? 'Order created successfully'
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Steadfast response returned error: ' . $response->body()
                ];
            }

            return [
                'success' => false,
                'message' => 'Steadfast API connection failed with status code: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error("Steadfast API Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Steadfast API error: ' . $e->getMessage()
            ];
        }
    }

    public static function sendToPathao($order, $codAmount, $note = '')
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $clientId = $settings['pathao_client_id'] ?? '';
        $clientSecret = $settings['pathao_client_secret'] ?? '';
        $username = $settings['pathao_username'] ?? '';
        $password = $settings['pathao_password'] ?? '';
        $storeId = $settings['pathao_store_id'] ?? '';
        $isSandbox = filter_var($settings['pathao_sandbox'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $details = self::parseShippingDetails($order->shipping_address);

        if (empty($clientId) || empty($clientSecret) || empty($username) || empty($password) || empty($storeId)) {
            Log::info("Pathao credentials missing. Falling back to Mock Pathao consignment.");
            return [
                'success' => true,
                'tracking_code' => 'MOCK-PTHO-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                'status' => 'mocked',
                'message' => 'Consignment created successfully (MOCK MODE - Credentials missing)'
            ];
        }

        $baseUrl = $isSandbox ? 'https://hermes-api.sandbox.pathao.com' : 'https://openapi.pathao.com';

        try {
            // 1. Get Access Token
            $tokenResponse = Http::post($baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'username' => $username,
                'password' => $password,
                'grant_type' => 'password'
            ]);

            if (!$tokenResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'Pathao token authentication failed: ' . $tokenResponse->body()
                ];
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? '';

            if (empty($accessToken)) {
                return [
                    'success' => false,
                    'message' => 'Pathao token response did not contain access_token.'
                ];
            }

            // 2. Create Order (Note: Pathao requires city, zone, area IDs. If address location parsing fails, we default to 1 (Dhaka City)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($baseUrl . '/aladdin/api/v1/orders', [
                'store_id' => (int)$storeId,
                'merchant_order_id' => 'ORDER-' . $order->id,
                'recipient_name' => $details['name'],
                'recipient_phone' => $details['phone'],
                'recipient_address' => $details['address'],
                'recipient_city' => 1, // Defaulting to Dhaka City (ID: 1)
                'recipient_zone' => 1, // Defaulting to Zone 1
                'recipient_area' => 1, // Defaulting to Area 1
                'delivery_type' => 48, // Standard 48 Hours
                'item_type' => 1, // Document/Parcel
                'special_instruction' => $note ?: 'Delivering order #' . $order->id,
                'item_quantity' => 1,
                'item_weight' => 0.5,
                'amount_to_collect' => (float)$codAmount
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['consignment_id'])) {
                    return [
                        'success' => true,
                        'tracking_code' => $data['data']['consignment_id'],
                        'status' => $data['data']['status'] ?? 'pending',
                        'message' => 'Pathao consignment created successfully'
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Pathao API responded with error: ' . $response->body()
                ];
            }

            return [
                'success' => false,
                'message' => 'Pathao order creation failed with status: ' . $response->status() . ' - ' . $response->body()
            ];
        } catch (\Exception $e) {
            Log::error("Pathao API Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Pathao API error: ' . $e->getMessage()
            ];
        }
    }
}
