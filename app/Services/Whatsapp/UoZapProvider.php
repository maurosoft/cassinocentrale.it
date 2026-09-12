<?php

namespace App\Services\Whatsapp;

class UoZapProvider extends AbstractHttpProvider
{
    protected function defaultBaseUrl(): string
    {
        return 'https://uozap.it'; // senza www (il www fa un redirect che svuota il body)
    }

    protected function formatNumber(string $number): string
    {
        return $this->digitsOnly($number); // solo cifre, senza "+"
    }

    protected function buildRequest(string $to, string $message): array
    {
        $contact = ['number' => $to];
        if (! empty($this->config->instance_id)) {
            $contact['gateway_identifier'] = $this->config->instance_id;
        }
        $contact['message'] = $message;

        return [
            'url' => $this->baseUrl().'/api/whatsapp/send',
            'headers' => [
                'Api-key' => $this->config->token,
                'Content-Type' => 'application/json',
            ],
            'body' => ['contact' => [$contact]],
            'no_redirect' => true,
        ];
    }

    protected function succeeded(int $status, array $json): bool
    {
        if ($status < 200 || $status >= 300) {
            return false;
        }

        return ($json['success'] ?? null) === true
            || ($json['status'] ?? null) === 'success';
    }
}
