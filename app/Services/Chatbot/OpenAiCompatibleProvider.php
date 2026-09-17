<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;

/**
 * Provider compatibili con lo schema OpenAI: OpenAI, DeepSeek, OpenRouter.
 * Stesso formato di richiesta/risposta, cambia solo l'URL di base.
 */
class OpenAiCompatibleProvider implements ChatProvider
{
    public function __construct(
        private string $apiKey,
        private string $baseUrl,
        private array $extraHeaders = [],
    ) {}

    public function chat(string $system, array $messages, string $model): array
    {
        $payload = [
            'model' => $model,
            'messages' => array_merge([['role' => 'system', 'content' => $system]], $messages),
            'temperature' => 0.4,
            'max_tokens' => 800,
        ];

        try {
            $response = Http::timeout(30)
                ->withToken($this->apiKey)
                ->withHeaders($this->extraHeaders)
                ->acceptJson()
                ->post(rtrim($this->baseUrl, '/').'/chat/completions', $payload);

            if (! $response->successful()) {
                return ['ok' => false, 'error' => 'HTTP '.$response->status().' · '.mb_substr($response->body(), 0, 300)];
            }

            $reply = data_get($response->json(), 'choices.0.message.content');

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
                ->withToken($this->apiKey)
                ->withHeaders($this->extraHeaders)
                ->acceptJson()
                ->get(rtrim($this->baseUrl, '/').'/models');

            if (! $response->successful()) {
                return [];
            }

            return collect(data_get($response->json(), 'data', []))
                ->pluck('id')
                ->filter()
                ->sort()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
