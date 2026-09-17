<?php

namespace App\Services\Chatbot;

/**
 * Elenco dei provider AI supportati e fabbrica per crearli.
 * Usato sia dal pannello admin (per mostrare le opzioni) sia dal servizio.
 */
class Providers
{
    /**
     * Metadati di ogni provider.
     *  - label: nome mostrato in admin
     *  - base:  URL di base delle API
     *  - kind:  schema tecnico (openai | anthropic | gemini)
     *  - help:  dove ottenere la chiave
     *  - headers: header extra (es. OpenRouter)
     */
    public const ALL = [
        'deepseek' => [
            'label' => 'DeepSeek',
            'base' => 'https://api.deepseek.com',
            'kind' => 'openai',
            'help' => 'platform.deepseek.com → API keys',
        ],
        'anthropic' => [
            'label' => 'Anthropic (Claude)',
            'base' => 'https://api.anthropic.com',
            'kind' => 'anthropic',
            'help' => 'console.anthropic.com → API keys',
        ],
        'openai' => [
            'label' => 'OpenAI (ChatGPT)',
            'base' => 'https://api.openai.com/v1',
            'kind' => 'openai',
            'help' => 'platform.openai.com → API keys',
        ],
        'gemini' => [
            'label' => 'Google Gemini',
            'base' => 'https://generativelanguage.googleapis.com',
            'kind' => 'gemini',
            'help' => 'aistudio.google.com → Get API key',
        ],
        'openrouter' => [
            'label' => 'OpenRouter',
            'base' => 'https://openrouter.ai/api/v1',
            'kind' => 'openai',
            'help' => 'openrouter.ai → Keys',
            'headers' => [
                'HTTP-Referer' => 'https://cassinocentrale.it',
                'X-Title' => 'B&B Cassino Centrale',
            ],
        ],
    ];

    public static function exists(string $key): bool
    {
        return isset(self::ALL[$key]);
    }

    /** Crea il provider concreto con la chiave data (o null se non valido). */
    public static function make(string $key, string $apiKey): ?ChatProvider
    {
        $p = self::ALL[$key] ?? null;

        if (! $p || $apiKey === '') {
            return null;
        }

        return match ($p['kind']) {
            'anthropic' => new AnthropicProvider($apiKey, $p['base']),
            'gemini' => new GeminiProvider($apiKey, $p['base']),
            default => new OpenAiCompatibleProvider($apiKey, $p['base'], $p['headers'] ?? []),
        };
    }
}
