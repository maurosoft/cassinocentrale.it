<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;

/**
 * Provider Google Gemini. Formato diverso: "contents" con ruoli user/model,
 * chiave passata come parametro nell'URL.
 */
class GeminiProvider implements ChatProvider
{
    public function __construct(
        private string $apiKey,
        private string $baseUrl = 'https://generativelanguage.googleapis.com',
    ) {}

    public function chat(string $system, array $messages, string $model): array
    {
        $contents = array_map(fn ($m) => [
            'role' => ($m['role'] ?? 'user') === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => (string) ($m['content'] ?? '')]],
        ], $messages);

        $payload = [
            'system_instruction' => ['parts' => [['text' => $system]]],
            'contents' => $contents,
            'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 800],
        ];

        $url = rtrim($this->baseUrl, '/').'/v1beta/models/'.$model.':generateContent?key='.$this->apiKey;

        try {
            $response = Http::timeout(30)->acceptJson()->post($url, $payload);

            if (! $response->successful()) {
                return ['ok' => false, 'error' => 'HTTP '.$response->status().' · '.mb_substr($response->body(), 0, 300)];
            }

            $reply = data_get($response->json(), 'candidates.0.content.parts.0.text');

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
            $response = Http::timeout(20)->acceptJson()
                ->get(rtrim($this->baseUrl, '/').'/v1beta/models?key='.$this->apiKey);

            if (! $response->successful()) {
                return [];
            }

            return collect(data_get($response->json(), 'models', []))
                ->filter(fn ($m) => in_array('generateContent', data_get($m, 'supportedGenerationMethods', []), true))
                ->map(fn ($m) => str_replace('models/', '', (string) data_get($m, 'name')))
                ->filter()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
