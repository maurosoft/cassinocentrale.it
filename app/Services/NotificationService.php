<?php

namespace App\Services;

use App\Mail\BookingMail;
use App\Models\Booking;
use App\Models\EmailLog;
use App\Support\Settings;
use Illuminate\Support\Facades\Mail;

/**
 * Punto unico per l'invio delle notifiche (email + WhatsApp).
 * Ogni invio rispetta gli interruttori impostati in admin (site_settings 'notify.*').
 */
class NotificationService
{
    public function __construct(private readonly WhatsAppService $whatsapp) {}

    public function bookingCreated(Booking $booking): void
    {
        $this->dispatch($booking, 'new', 'new');
    }

    public function bookingUpdated(Booking $booking): void
    {
        $this->dispatch($booking, 'updated', 'change');
    }

    public function bookingCancelled(Booking $booking): void
    {
        $this->dispatch($booking, 'cancelled', 'cancel');
    }

    private function dispatch(Booking $booking, string $event, string $suffix): void
    {
        $emailOn = (bool) Settings::get('notify.email_'.$suffix, false);
        $waOn = (bool) Settings::get('notify.whatsapp_'.$suffix, false);

        if (! $emailOn && ! $waOn) {
            return;
        }

        $booking->loadMissing('rooms.room');

        if ($emailOn) {
            if (! empty($booking->guest_email)) {
                $this->sendEmail($booking->guest_email, new BookingMail($booking, 'guest', $event), $event);
            }
            $adminEmail = Settings::get('notify.admin_email') ?: config('bnb.contact.email');
            if (! empty($adminEmail)) {
                $this->sendEmail($adminEmail, new BookingMail($booking, 'admin', $event), $event);
            }
        }

        if ($waOn) {
            if (! empty($booking->guest_phone)) {
                $this->whatsapp->send($booking->guest_phone, $this->whatsappText($booking, 'guest', $event));
            }
            $adminWa = Settings::get('notify.admin_whatsapp');
            if (! empty($adminWa)) {
                $this->whatsapp->send($adminWa, $this->whatsappText($booking, 'admin', $event));
            }
        }
    }

    private function sendEmail(string $to, BookingMail $mail, string $event): void
    {
        try {
            Mail::to($to)->send($mail);
            EmailLog::create(['to' => $to, 'subject' => $mail->subjectLine(), 'event' => $event, 'status' => 'sent']);
        } catch (\Throwable $e) {
            EmailLog::create(['to' => $to, 'subject' => $mail->subjectLine(), 'event' => $event, 'status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 1000)]);
            report($e);
        }
    }

    /** Invia un'email di prova per verificare la configurazione SMTP. */
    public function sendTest(string $to): bool
    {
        try {
            Mail::raw('Email di test dal sito B&B Cassino Centrale. Se leggi questo messaggio, la configurazione SMTP funziona correttamente!',
                fn ($m) => $m->to($to)->subject('Test email · B&B Cassino Centrale'));
            EmailLog::create(['to' => $to, 'subject' => 'Test email', 'event' => 'test', 'status' => 'sent']);

            return true;
        } catch (\Throwable $e) {
            EmailLog::create(['to' => $to, 'subject' => 'Test email', 'event' => 'test', 'status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 1000)]);

            return false;
        }
    }

    /** Testo del messaggio WhatsApp. */
    private function whatsappText(Booking $booking, string $audience, string $event): string
    {
        $rooms = $booking->rooms->map(fn ($r) => $r->room?->number_name)->filter()->join(', ');
        $period = $booking->check_in->format('d/m/Y').' → '.$booking->check_out->format('d/m/Y');

        $head = match ("{$audience}.{$event}") {
            'guest.new' => "Grazie! Abbiamo ricevuto la tua richiesta di prenotazione ({$booking->reference}). Ti confermiamo a breve.",
            'guest.updated' => "La tua prenotazione ({$booking->reference}) è stata aggiornata.",
            'guest.cancelled' => "La tua prenotazione ({$booking->reference}) è stata annullata.",
            'admin.new' => "Nuova richiesta di prenotazione ({$booking->reference}).",
            'admin.updated' => "Prenotazione modificata ({$booking->reference}).",
            'admin.cancelled' => "Prenotazione annullata ({$booking->reference}).",
            default => "Prenotazione {$booking->reference}.",
        };

        $body = "{$head}\n\n"
            ."👤 {$booking->guest_name}\n"
            ."🛏️ Camere: {$rooms}\n"
            ."📅 {$period} ({$booking->nights()} notti)\n"
            ."👥 Ospiti: {$booking->number_of_guests}\n"
            .'💶 Totale: €'.number_format($booking->total_price, 2, ',', '.');

        if ($audience === 'guest') {
            $body .= "\n\nB&B Cassino Centrale · ".config('bnb.contact.phone');
        } elseif ($booking->guest_phone) {
            $body .= "\n📞 {$booking->guest_phone}";
        }

        return $body;
    }
}
