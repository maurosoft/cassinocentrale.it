<?php

namespace App\Services\Whatsapp;

/**
 * Hooki (e SpotWab, stesso contratto: cambia solo la base_url).
 */
class HookiProvider extends AbstractHttpProvider
{
    protected function defaultBaseUrl(): string
    {
        return 'https://api.hooki.pro';
    }

    protected function formatNumber(string $number): string
    {
        return '+'.$this->digitsOnly($number); // formato +E164
    }

    protected function buildRequest(string $to, string $message): array
    {
        $body = ['to' => $to, 'message' => $message];
        if (! empty($this->config->instance_id)) {
            $body['sessionId'] = $this->config->instance_id;
        }

        return [
            'url' => $this->baseUrl().'/v1/ext/messages/send',
            'headers' => [
                'Authorization' => 'Bearer '.$this->config->token,
                'Content-Type' => 'application/json',
            ],
            'body' => $body,
        ];
    }

    protected function succeeded(int $status, array $json): bool
    {
        if ($status < 200 || $status >= 300) {
            return false;
        }

        return ($json['sent'] ?? null) !== false;
    }
}
