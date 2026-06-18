<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFacebookCapiEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pixelId;
    public $accessToken;
    public $payload;

    /**
     * Create a new job instance.
     *
     * @param string $pixelId
     * @param string $accessToken
     * @param array $payload
     */
    public function __construct(string $pixelId, string $accessToken, array $payload)
    {
        $this->pixelId = $pixelId;
        $this->accessToken = $accessToken;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = "https://graph.facebook.com/v19.0/{$this->pixelId}/events?access_token={$this->accessToken}";
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $this->payload);

            if ($response->failed()) {
                Log::error('Meta Graph API CAPI Event failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Meta Graph API CAPI Event Exception: ' . $e->getMessage());
        }
    }
}
