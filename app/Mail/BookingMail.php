<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $audience  'guest' | 'admin'
     * @param  string  $event     'new' | 'updated' | 'cancelled'
     */
    public function __construct(
        public Booking $booking,
        public string $audience = 'guest',
        public string $event = 'new',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine());
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking',
            with: [
                'booking' => $this->booking,
                'audience' => $this->audience,
                'event' => $this->event,
            ],
        );
    }

    public function subjectLine(): string
    {
        $ref = $this->booking->reference;

        return match ("{$this->audience}.{$this->event}") {
            'guest.new' => "Abbiamo ricevuto la tua richiesta · {$ref}",
            'guest.updated' => "La tua prenotazione è stata aggiornata · {$ref}",
            'guest.cancelled' => "La tua prenotazione è stata annullata · {$ref}",
            'admin.new' => "Nuova richiesta di prenotazione · {$ref}",
            'admin.updated' => "Prenotazione modificata · {$ref}",
            'admin.cancelled' => "Prenotazione annullata · {$ref}",
            default => "Prenotazione · {$ref}",
        };
    }
}
