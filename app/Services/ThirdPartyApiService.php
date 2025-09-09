<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThirdPartyApiService
{
    /**
     * Lähetä tilaus mock-API:lle turvallisesti
     *
     * @param array $orderData
     * @return array
     */
    public function sendOrder(array $orderData): array
    {
        try {
            // ===================================================
            // Mock API URL voidaan konfiguroida .env-tiedostosta
            // ===================================================
            $url = config('services.mock_api.url', 'http://mock-api:5000/orders');

            // ===================================================
            // Lähetetään POST-pyyntö, timeout asetettu 35 sekuntiin
            // Estetään mahdolliset injektiot ja varmennetaan JSON
            // ===================================================
            $response = Http::timeout(35)
                ->acceptJson()
                ->post($url, [
                    'order_name' => e($orderData['order_name'] ?? ''),
                    'meta' => $orderData['meta'] ?? null,
                    'recurring' => $orderData['recurring'] ?? false,
                ]);

            // ===================================================
            // Jos epäonnistuu, logitetaan ja palautetaan turvallinen viesti
            // ===================================================
            if ($response->failed()) {
                Log::warning('Mock API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'data' => $orderData
                ]);

                return [
                    'success' => false,
                    'message' => 'Kolmannen osapuolen API epäonnistui',
                    'status' => $response->status()
                ];
            }

            // ===================================================
            // Palautetaan JSON-data, lisätään aina success:true
            // ===================================================
            $result = $response->json();
            return array_merge(['success' => true], $result);

        } catch (\Exception $e) {
            // ===================================================
            // Kaikki poikkeukset logitetaan
            // ===================================================
            Log::error('Exception when sending order to mock API', [
                'error' => $e->getMessage(),
                'data' => $orderData
            ]);

            return [
                'success' => false,
                'message' => 'Virhe kolmannen osapuolen API:in: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }
}
