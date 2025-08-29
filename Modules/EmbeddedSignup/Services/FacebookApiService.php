<?php

namespace Modules\EmbeddedSignup\Services;

use Illuminate\Support\Facades\Http;

class FacebookApiService
{
    protected $apiVersion = 'v21.0';

    protected $baseUrl = 'https://graph.facebook.com';

    /**
     * Test access token validity
     */
    public function testAccessToken(string $accessToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/me", [
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }

    /**
     * Exchange authorization code for access token
     */
    public function exchangeCodeForToken(string $code, string $appId, string $appSecret): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/oauth/access_token", [
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'code' => $code,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }

    /**
     * Get WhatsApp Business Accounts
     */
    public function getWhatsAppBusinessAccounts(string $accessToken): array
    {
        try {
            // First, try to get user's businesses
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/me/businesses", [
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $businesses = $response->json();

                // If we have businesses, get WABA for each
                if (! empty($businesses['data'])) {
                    $businessId = $businesses['data'][0]['id'];

                    // Get WhatsApp Business Accounts for this business
                    $wabaResponse = Http::get("{$this->baseUrl}/{$this->apiVersion}/{$businessId}", [
                        'fields' => 'whatsapp_business_accounts{id,name,phone_numbers{id,display_phone_number,verified_name,status,platform_type}}',
                        'access_token' => $accessToken,
                    ]);

                    if ($wabaResponse->successful()) {
                        return [
                            'success' => true,
                            'data' => ['data' => [$wabaResponse->json()]],
                        ];
                    }
                }
            }

            // Fallback: Try direct WABA access
            $directResponse = Http::get("{$this->baseUrl}/{$this->apiVersion}/me", [
                'fields' => 'whatsapp_business_accounts{id,name,phone_numbers{id,display_phone_number,verified_name,status,platform_type}}',
                'access_token' => $accessToken,
            ]);

            if ($directResponse->successful()) {
                return [
                    'success' => true,
                    'data' => $directResponse->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }

    /**
     * Get phone number details
     */
    public function getPhoneNumberDetails(string $phoneNumberId, string $accessToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$this->apiVersion}/{$phoneNumberId}", [
                'fields' => 'id,display_phone_number,verified_name,platform_type,status',
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }

    /**
     * Register phone number for Cloud API
     */
    public function registerPhoneNumber(string $phoneNumberId, string $accessToken): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/{$phoneNumberId}/register", [
                'messaging_product' => 'whatsapp',
                'access_token' => $accessToken,
                'pin' => '123456',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }

    /**
     * Subscribe to webhooks
     */
    public function subscribeToWebhooks(string $wabaId, string $accessToken, string $webhookUrl, string $verifyToken): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/{$this->apiVersion}/{$wabaId}/subscribed_apps", [
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }
}
