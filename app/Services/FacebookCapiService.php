<?php

namespace App\Services;

use App\Models\Setting;
use App\Jobs\SendFacebookCapiEvent;
use Illuminate\Support\Str;

class FacebookCapiService
{
    /**
     * Formats and dispatches a Facebook CAPI event to the queue.
     *
     * @param string $eventName Standard Facebook event (e.g. ViewContent, AddToCart, InitiateCheckout, Purchase)
     * @param string $eventId Deduplication event ID shared with the browser pixel
     * @param array $customData Dynamic event parameters (value, currency, content_type, contents)
     * @param array $userData Optional customer details for matching (em, ph, fn, etc.)
     * @return void
     */
    public static function sendEvent(string $eventName, string $eventId, array $customData = [], array $userData = [])
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        $capiEnabled = filter_var($settings['facebook_capi_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $pixelId = $settings['facebook_pixel_id'] ?? '';
        $accessToken = $settings['facebook_capi_token'] ?? '';
        $testEventCode = $settings['facebook_capi_test_code'] ?? '';

        if (!$capiEnabled || empty($pixelId) || empty($accessToken)) {
            return;
        }

        // Gather base user data from request context
        $finalUserData = array_merge([
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
            'fbp' => request()->cookie('_fbp'),
            'fbc' => request()->cookie('_fbc'),
        ], $userData);

        // Normalize and SHA-256 hash required customer keys
        $hashedUserData = [];
        foreach ($finalUserData as $key => $val) {
            if ($val === null) {
                continue;
            }

            $cleanKey = strtolower(trim($key));
            if (in_array($cleanKey, ['em', 'ph', 'fn', 'ln', 'ge', 'db', 'ct', 'st', 'zp', 'country'])) {
                $hashedUserData[$cleanKey] = hash('sha256', strtolower(trim($val)));
            } else {
                $hashedUserData[$cleanKey] = $val;
            }
        }

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_id' => $eventId,
                    'event_source_url' => request()->fullUrl(),
                    'action_source' => 'website',
                    'user_data' => $hashedUserData,
                    'custom_data' => $customData,
                ]
            ]
        ];

        if (!empty($testEventCode)) {
            $payload['test_event_code'] = $testEventCode;
        }

        // Dispatch background queued job
        SendFacebookCapiEvent::dispatch($pixelId, $accessToken, $payload);
    }
}
