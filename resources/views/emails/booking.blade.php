@php
    $bnb = config('bnb');
    $intro = match ("{$audience}.{$event}") {
        'guest.new' => 'Grazie! Abbiamo ricevuto la tua richiesta di prenotazione. Ti contatteremo al più presto per confermare la disponibilità. Il pagamento si effettua in struttura.',
        'guest.updated' => 'La tua prenotazione è stata aggiornata. Trovi qui sotto i dettagli aggiornati.',
        'guest.cancelled' => 'La tua prenotazione è stata annullata. Per qualsiasi informazione siamo a tua disposizione.',
        'admin.new' => 'È arrivata una nuova richiesta di prenotazione dal sito.',
        'admin.updated' => 'Una prenotazione è stata modificata.',
        'admin.cancelled' => 'Una prenotazione è stata annullata.',
        default => '',
    };
@endphp
<!DOCTYPE html>
<html lang="it">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0; background:#faf4ea; font-family:Arial,Helvetica,sans-serif; color:#3a2e26;">
    <div style="max-width:600px; margin:0 auto; padding:24px;">
        <div style="background:#8f4429; color:#faf4ea; padding:20px 24px; border-radius:14px 14px 0 0;">
            <div style="font-size:18px; font-weight:bold;">B&amp;B Cassino Centrale</div>
            <div style="font-size:12px; opacity:.85;">Nel Cuore della Città</div>
        </div>

        <div style="background:#ffffff; border:1px solid #e9d9bd; border-top:0; border-radius:0 0 14px 14px; padding:24px;">
            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">{{ $intro }}</p>

            <table role="presentation" width="100%" style="border-collapse:collapse; font-size:14px;">
                <tr><td style="padding:6px 0; color:#8a7864;">Codice</td><td style="padding:6px 0; text-align:right; font-weight:bold; color:#8f4429;">{{ $booking->reference }}</td></tr>
                <tr><td style="padding:6px 0; color:#8a7864;">Ospite</td><td style="padding:6px 0; text-align:right;">{{ $booking->guest_name }}</td></tr>
                <tr><td style="padding:6px 0; color:#8a7864;">Arrivo</td><td style="padding:6px 0; text-align:right;">{{ $booking->check_in->format('d/m/Y') }}</td></tr>
                <tr><td style="padding:6px 0; color:#8a7864;">Partenza</td><td style="padding:6px 0; text-align:right;">{{ $booking->check_out->format('d/m/Y') }}</td></tr>
                <tr><td style="padding:6px 0; color:#8a7864;">Notti</td><td style="padding:6px 0; text-align:right;">{{ $booking->nights() }}</td></tr>
                <tr><td style="padding:6px 0; color:#8a7864;">Ospiti</td><td style="padding:6px 0; text-align:right;">{{ $booking->number_of_guests }}</td></tr>
                <tr><td style="padding:6px 0; color:#8a7864;">Camere</td><td style="padding:6px 0; text-align:right;">{{ $booking->rooms->map(fn ($r) => $r->room?->number_name)->filter()->join(', ') }}</td></tr>
                <tr><td style="padding:10px 0 0; color:#8a7864; border-top:1px solid #f3e9d6;">Totale indicativo</td><td style="padding:10px 0 0; text-align:right; font-weight:bold; font-size:16px; color:#8f4429; border-top:1px solid #f3e9d6;">€{{ number_format($booking->total_price, 2, ',', '.') }}</td></tr>
            </table>

            @if ($booking->notes)
                <p style="margin:16px 0 0; font-size:13px; color:#5c4a3a;"><strong>Note:</strong> {{ $booking->notes }}</p>
            @endif

            @if ($audience === 'admin')
                <p style="margin:16px 0 0; font-size:13px; color:#5c4a3a;">Contatti ospite:
                    @if ($booking->guest_email) {{ $booking->guest_email }} @endif
                    @if ($booking->guest_phone) · {{ $booking->guest_phone }} @endif
                </p>
            @else
                <p style="margin:20px 0 0; font-size:13px; color:#5c4a3a; line-height:1.6;">
                    Per qualsiasi cosa: {{ $bnb['contact']['phone'] }} · {{ $bnb['contact']['email'] }}<br>
                    {{ $bnb['contact']['address'] }}
                </p>
            @endif
        </div>

        <p style="text-align:center; font-size:11px; color:#8a7864; margin:16px 0 0;">B&amp;B Cassino Centrale · {{ $bnb['contact']['address'] }}</p>
    </div>
</body>
</html>
