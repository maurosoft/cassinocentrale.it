<?php

namespace App\Services;

use App\Mail\BookingMail;
use App\Models\Booking;
use App\Models\EmailLog;
use App\Support\Settings;
use Illuminate\Support\Facades\Mail;

/**
 * Punto unico per l'invio delle notifiche (email ora, WhatsApp in Fase 4b).
 * Ogni invio rispetta gli interruttori impostati in admin (site_settings 'notify.*').
 */
class NotificationService
{
    public function bookingCreated(Booking $booking): void
    {
        $this->dispatch($booking, 'new', 'email_new');
    }

    public function bookingUpdated(Booking $booking): void
    {
        $this->dispatch($booking, 'updated', 'email_change');
    }

    public function bookingCancelled(Booking $booking): void
    {
        $this->dispatch($booking, 'cancelled', 'email_cancel');
    }

    private function dispatch(Booking $booking, string $event, string $toggle): void
    {
        if (! Settings::get('notify.'.$toggle, false)) {
            return; // notifica disattivata da admin
        }

        $booking->loadMissing('rooms.room');

        // Email al cliente
        if (! empty($booking->guest_email)) {
            $this->sendEmail($booking->guest_email, new BookingMail($booking, 'guest', $event), $event);
        }

        // Email allo staff/struttura
        $adminEmail = Settings::get('notify.admin_email') ?: config('bnb.contact.email');
        if (! empty($adminEmail)) {
            $this->sendEmail($adminEmail, new BookingMail($booking, 'admin', $event), $event);
        }

        // TODO Fase 4b: invio WhatsApp con provider multipli + fallback.
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
}
