<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\StripeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StripeController extends Controller
{
    public function __construct(private readonly StripeService $stripe) {}

    /** Avvia il pagamento: crea la sessione e reindirizza a Stripe. */
    public function pay(string $reference): RedirectResponse
    {
        $booking = Booking::where('reference', $reference)->firstOrFail();

        if (! $this->stripe->enabled()) {
            return redirect()->route('booking.confirmation', $reference)
                ->with('error', 'Il pagamento online non è al momento disponibile.');
        }

        $url = $this->stripe->createCheckoutUrl(
            $booking,
            route('booking.paid', $reference),
            route('booking.confirmation', $reference),
        );

        if (! $url) {
            return redirect()->route('booking.confirmation', $reference)
                ->with('error', 'Non è stato possibile avviare il pagamento. Riprova o contattaci.');
        }

        return redirect()->away($url);
    }

    /** Pagina di ritorno dopo il pagamento (la conferma vera arriva dal webhook). */
    public function paid(string $reference): View
    {
        $booking = Booking::with('rooms.room')->where('reference', $reference)->firstOrFail();

        return view('bookings.paid', compact('booking'));
    }

    /** Webhook di Stripe: conferma il pagamento in modo affidabile. */
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();

        if (! $this->stripe->verifyWebhookSignature($payload, $request->header('Stripe-Signature'))) {
            return response('Firma non valida', 400);
        }

        $event = json_decode($payload, true);

        if (($event['type'] ?? null) === 'checkout.session.completed') {
            $session = $event['data']['object'] ?? [];
            $reference = $session['client_reference_id'] ?? ($session['metadata']['booking_reference'] ?? null);

            if ($reference && $booking = Booking::where('reference', $reference)->first()) {
                $booking->update([
                    'payment_status' => Booking::PAYMENT_PAID,
                    'status' => Booking::STATUS_CONFIRMED,
                    'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
                ]);
            }
        }

        return response('ok', 200);
    }
}
