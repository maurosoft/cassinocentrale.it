<?php

namespace App\Services;

use App\Models\WhatsappLog;
use App\Models\WhatsappProvider;
use App\Services\Whatsapp\AbstractHttpProvider;
use App\Services\Whatsapp\HookiProvider;
use App\Services\Whatsapp\UoZapProvider;
use App\Support\Settings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WhatsAppService
{
    /** Provider attivi in ordine di fallback. */
    public function providersInOrder(): Collection
    {
        return WhatsappProvider::where('is_active', true)
            ->orderBy('order_fallback')
            ->orderBy('id')
            ->get();
    }

    public function isEnabled(): bool
    {
        return (bool) Settings::get('whatsapp.enabled', false)
            && $this->providersInOrder()->isNotEmpty();
    }

    /**
     * Invia un messaggio provando i provider in ordine (fallback).
     * Ritorna true se almeno un provider ha avuto successo.
     */
    public function send(string $number, string $message): bool
    {
        if (blank($number) || ! $this->isEnabled()) {
            return false;
        }

        foreach ($this->providersInOrder() as $config) {
            $result = $this->make($config)->send($number, $message);

            WhatsappLog::create([
                'provider' => $config->provider,
                'to' => $number,
                'message' => Str::limit($message, 500),
                'status' => $result['ok'] ? 'sent' : 'failed',
                'response' => Str::limit($result['response'] ?? '', 1000),
            ]);

            if ($result['ok']) {
                return true; // inviato: stop al fallback
            }
        }

        return false; // tutti i provider hanno fallito
    }

    /** Invio di prova su un singolo provider (dal pannello admin). */
    public function testSend(WhatsappProvider $config, string $number, string $message): array
    {
        $result = $this->make($config)->send($number, $message);

        WhatsappLog::create([
            'provider' => $config->provider,
            'to' => $number,
            'message' => Str::limit($message, 500),
            'status' => $result['ok'] ? 'sent' : 'failed',
            'response' => Str::limit($result['response'] ?? '', 1000),
        ]);

        return $result;
    }

    private function make(WhatsappProvider $config): AbstractHttpProvider
    {
        return match ($config->provider) {
            'hooki' => new HookiProvider($config),
            default => new UoZapProvider($config),
        };
    }
}
