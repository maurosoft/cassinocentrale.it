<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;

/**
 * Provider Anthropic (Claude). Usa /v1/messages con header x-api-key
 * e "system" separato dai messaggi.
 */
class AnthropicProvider implements ChatProvider
{
    public function __construct(
        private string $apiKey,
        private string $baseUrl = 'https://api.anthropic.com',
    ) {}

    private function headers(): array
    {
        return [
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
        ];
    }

    public function chat(string $system, array $messages, string $model): array
    {
        $payload = [
            'model' => $model,
            'max_tokens' => 800,
            'system' => $system,
            'messages' => $messages,
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders($this->headers())
                ->acceptJson()
                ->post(rtrim($this->baseUrl, '/').'/v1/messages', $payload);

            if (! $response->successful()) {
                return ['ok' => false, 'error' => 'HTTP '.$response->status().' · '.mb_substr($response->body(), 0, 300)];
            }

            $reply = data_get($response->json(), 'content.0.text');

            return $reply
                ? ['ok' => true, 'reply' => trim($reply)]
                : ['ok' => false, 'error' => 'Risposta vuota dal provider.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => mb_substr($e->getMessage(), 0, 300)];
        }
    }

    public function models(): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders($this->headers())
                ->acceptJson()
                ->get(rtrim($this->baseUrl, '/').'/v1/models');

            if (! $response->successful()) {
                return [];
            }

            return collect(data_get($response->json(), 'data', []))
                ->pluck('id')
                ->filter()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
