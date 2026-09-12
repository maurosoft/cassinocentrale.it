@extends('layouts.app')

@section('title', 'Termini e Condizioni · '.$bnb['name'])
@section('meta_description', 'Termini e condizioni di utilizzo del sito e di prenotazione del B&B Cassino Centrale.')

@section('content')
    <section class="bg-cream-50 py-12">
        <div class="container-bnb">
            <span class="eyebrow">Informativa</span>
            <h1 class="section-title">Termini e Condizioni</h1>
            <p class="mt-2 text-sm text-ink-soft">Ultimo aggiornamento: {{ now()->translatedFormat('F Y') }}</p>
        </div>
    </section>

    <section class="container-bnb prose-bnb py-12">
        <h2>Oggetto</h2>
        <p>I presenti termini regolano l’utilizzo del sito del <strong>B&amp;B Cassino Centrale</strong> e le condizioni
        generali relative alle richieste di prenotazione del soggiorno.</p>

        <h2>Prenotazioni e tariffe</h2>
        <ul>
            <li>Le richieste inviate tramite il sito, telefono, email o WhatsApp non costituiscono conferma automatica:
            la prenotazione si intende confermata solo dopo il riscontro della struttura.</li>
            <li>Le tariffe indicate sono per camera, per notte, salvo diversa indicazione, e possono variare in base al
            periodo e alla durata del soggiorno.</li>
            <li>Eventuali sconti (ad esempio per soggiorni di più notti o per prenotazione diretta) sono applicati
            secondo le condizioni comunicate al momento della prenotazione.</li>
        </ul>

        <h2>Check-in e check-out</h2>
        <p>Check-in dalle {{ $bnb['checkin']['from'] }} alle {{ $bnb['checkin']['to'] }}; check-out entro le
        {{ $bnb['checkout']['until'] }}. Orari diversi possono essere concordati con la struttura.</p>

        <h2>Politica di cancellazione</h2>
        <p><em>[Da definire e inserire: termini di cancellazione, eventuali penali, modalità di pagamento e caparra.]</em></p>

        <h2>Regole della struttura</h2>
        <p>All’ospite è richiesto di rispettare gli ambienti, gli orari di quiete e le indicazioni della struttura.
        La direzione non risponde di beni personali non affidati in custodia.</p>

        <h2>Responsabilità</h2>
        <p>Ci impegniamo affinché le informazioni sul sito siano corrette e aggiornate, ma non garantiamo l’assenza di
        errori o interruzioni del servizio. Le immagini hanno finalità illustrativa.</p>

        <h2>Legge applicabile e foro competente</h2>
        <p>I presenti termini sono regolati dalla legge italiana. Per eventuali controversie è competente il foro del
        luogo in cui ha sede la struttura, salvo diverse disposizioni di legge a tutela del consumatore.</p>

        <h2>Contatti</h2>
        <p>Per qualsiasi informazione: {{ $bnb['contact']['phone'] }} ·
        <a href="mailto:{{ $bnb['contact']['email'] }}">{{ $bnb['contact']['email'] }}</a>.</p>

        <p class="text-sm text-ink-soft"><em>Documento di base fornito a titolo esemplificativo: ti consigliamo di
        completarlo (politica di cancellazione, pagamenti) e farlo verificare.</em></p>
    </section>
@endsection
