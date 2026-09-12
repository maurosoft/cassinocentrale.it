<?php

namespace App\Services\Whatsapp;

use App\Models\WhatsappProvider;
use Illuminate\Support\Facades\Http;

/**
 * Schema comune dei provider WhatsApp via HTTP.
 * Ogni provider concreto implementa solo: formatNumber, buildRequest, succeeded, defaultBaseUrl.
 */
abstract class AbstractHttpProvider
{
    public function __construct(protected WhatsappProvider $config) {}

    /** Normalizza il numero secondo il provider. */
    abstract protected function formatNumber(string $number): string;

    /**
     * Costruisce la richiesta: ['url'=>string, 'headers'=>array, 'body'=>array, 'no_redirect'=>bool]
     */
    abstract protected function buildRequest(string $to, string $message): array;

    /** Vero se la risposta indica un successo reale. */
    abstract protected function succeeded(int $status, array $json): bool;

    abstract protected function defaultBaseUrl(): string;

    protected function baseUrl(): string
    {
        return rtrim($this->config->base_url ?: $this->defaultBaseUrl(), '/');
    }

    /**
     * Invia il messaggio. Ritorna ['ok'=>bool, 'response'=>string].
     */
    public function send(string $number, string $message): array
    {
        $to = $this->formatNumber($number);
        $req = $this->buildRequest($to, $message);

        try {
            $http = Http::timeout($this->config->timeout ?: 15)
                ->acceptJson()
                ->withHeaders($req['headers'] ?? []);

            if (! empty($req['no_redirect'])) {
                $http = $http->withoutRedirecting();
            }

            $response = $http->post($req['url'], $req['body'] ?? []);
            $json = $response->json();
            $ok = $this->succeeded($response->status(), is_array($json) ? $json : []);

            return ['ok' => $ok, 'response' => 'HTTP '.$response->status().' · '.mb_substr($response->body(), 0, 500)];
        } catch (\Throwable $e) {
            return ['ok' => false, 'response' => 'Errore: '.mb_substr($e->getMessage(), 0, 500)];
        }
    }

    /** Solo cifre, con prefisso internazionale (39 per numeri italiani a 10 cifre). */
    protected function digitsOnly(string $number): string
    {
        $n = preg_replace('/\D+/', '', $number);
        if (str_starts_with($n, '00')) {
            $n = substr($n, 2);
        }
        if (strlen($n) === 10 && str_starts_with($n, '3')) {
            $n = '39'.$n; // numero di cellulare italiano senza prefisso
        }

        return $n;
    }
}
