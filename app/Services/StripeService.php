<?php

namespace App\Services;

use App\Models\Booking;
use App\Support\Settings;
use Illuminate\Support\Facades\Http;

/**
 * Integrazione Stripe via API HTTP (Stripe Checkout ospitato).
 * Nessuna libreria esterna: usa il client HTTP di Laravel.
 */
class StripeService
{
    private const API = 'https://api.stripe.com/v1';

    public function enabled(): bool
    {
        return (bool) Settings::get('stripe.enabled', false) && ! empty($this->secretKey());
    }

    private function secretKey(): ?string
    {
        return Settings::get('stripe.secret_key');
    }

    /** Percentuale da incassare online (100 = intero importo; es. 30 = caparra 30%). */
    private function depositPercent(): int
    {
        $p = (int) Settings::get('stripe.deposit_percent', 100);

        return max(1, min($p, 100));
    }

    /** Importo da pagare online, in centesimi. */
    public function amountCents(Booking $booking): int
    {
        return (int) round((float) $booking->total_price * $this->depositPercent() / 100 * 100);
    }

    /**
     * Crea una sessione di Stripe Checkout e ritorna l'URL a cui mandare il cliente.
     */
    public function createCheckoutUrl(Booking $booking, string $successUrl, string $cancelUrl): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        $deposit = $this->depositPercent();
        $label = $deposit < 100
            ? "Caparra {$deposit}% soggiorno {$booking->reference}"
            : "Soggiorno B&B Cassino Centrale · {$booking->reference}";

        $response = Http::asForm()
            ->withToken($this->secretKey())
            ->post(self::API.'/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $booking->reference,
                'customer_email' => $booking->guest_email,
                'line_items' => [[
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $this->amountCents($booking),
                        'product_data' => ['name' => $label],
                    ],
                ]],
                'metadata' => ['booking_reference' => $booking->reference],
            ]);

        if (! $response->successful()) {
            report(new \RuntimeException('Stripe checkout error: '.$response->body()));

            return null;
        }

        return $response->json('url');
    }

    /** Verifica la firma del webhook Stripe. */
    public function verifyWebhookSignature(string $payload, ?string $signatureHeader): bool
    {
        $secret = Settings::get('stripe.webhook_secret');
        if (empty($secret) || empty($signatureHeader)) {
            return false;
        }

        // Header formato: t=timestamp,v1=firma
        $parts = collect(explode(',', $signatureHeader))
            ->mapWithKeys(function ($p) {
                [$k, $v] = array_pad(explode('=', $p, 2), 2, null);

                return [trim((string) $k) => trim((string) $v)];
            });

        $timestamp = $parts['t'] ?? null;
        $v1 = $parts['v1'] ?? null;
        if (! $timestamp || ! $v1) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return hash_equals($expected, $v1);
    }
}
