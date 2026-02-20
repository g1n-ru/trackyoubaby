<?php

namespace App\Services;

use App\Models\Click;
use App\Models\Conversion;
use App\Models\MetricaSendLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class YandexMetricaService
{
    public function sendSessionParams(Click $click): bool
    {
        $counterId = Config::get('tracker.metrica.counter_id');
        $token = Config::get('tracker.metrica.token');
        $apiUrl = Config::get('tracker.metrica.api_url');

        $payload = [
            'counter_id' => $counterId,
            'client_id' => $click->ym_uid,
            'session_params' => [
                'click_id' => $click->click_id,
                'subid' => $click->subid,
            ],
        ];

        return $this->sendRequest($apiUrl, $token, $payload, $click->click_id, 'session_params');
    }

    public function sendConversion(Conversion $conversion, Click $click): bool
    {
        $counterId = Config::get('tracker.metrica.counter_id');
        $token = Config::get('tracker.metrica.token');
        $apiUrl = Config::get('tracker.metrica.api_url');

        $payload = [
            'counter_id' => $counterId,
            'client_id' => $click->ym_uid,
            'goal' => [
                'id' => 'conversion',
                'revenue' => $conversion->revenue,
                'currency' => $conversion->currency ?? 'RUB',
                'order_id' => $conversion->order_id,
            ],
        ];

        return $this->sendRequest($apiUrl, $token, $payload, $click->click_id, 'conversion');
    }

    public function logApiCall(
        string  $clickId,
        string  $eventType,
        array   $payload,
        ?int    $responseStatus,
        ?string $responseBody,
        bool    $success,
        int     $retryCount = 0,
        ?string $errorMessage = null
    ): MetricaSendLog
    {
        return MetricaSendLog::create([
            'click_id' => $clickId,
            'event_type' => $eventType,
            'request_payload' => $payload,
            'response_status' => $responseStatus,
            'response_body' => $responseBody,
            'success' => $success,
            'retry_count' => $retryCount,
            'error_message' => $errorMessage,
        ]);
    }

    private function sendRequest(string $url, string $token, array $payload, string $clickId, string $eventType): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'OAuth ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            $success = $response->successful();

            $this->logApiCall(
                $clickId,
                $eventType,
                $payload,
                $response->status(),
                $response->body(),
                $success,
            );

            return $success;

        } catch (Throwable $e) {
            Log::error('Yandex Metrica API error: ' . $e->getMessage());

            $this->logApiCall(
                $clickId,
                $eventType,
                $payload,
                null,
                null,
                false,
                0,
                $e->getMessage(),
            );

            return false;
        }
    }
}
