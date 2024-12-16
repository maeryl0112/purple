<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfobipSmsService
{
    protected $apiUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.infobip.api_url');
        $this->apiKey = config('services.infobip.api_key');
        $this->sender = config('services.infobip.sender');
    }

    /**
     * Send an SMS message via Infobip API.
     *
     * @param string $to Recipient phone number (e.g., "639107171482")
     * @param string $text SMS message content
     * @return array|null Response from Infobip API
     * @throws \Exception
     */
    public function sendSms(string $to, string $text): ?array
    {
        $payload = [
            'messages' => [
                [
                    'destinations' => [
                        ['to' => $to],
                    ],
                    'from' => $this->sender,
                    'text' => $text,
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->apiUrl, $payload);

            // Check for success (status 200-299)
            if ($response->successful()) {
                return $response->json();
            }

            // Log error details for debugging
            Log::error("Infobip SMS failed", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            throw new \Exception("Infobip SMS API returned an error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Infobip SMS Exception: " . $e->getMessage());
            throw $e; // Re-throw the exception for higher-level handling
        }
    }
}
