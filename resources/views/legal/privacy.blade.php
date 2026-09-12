@extends('layouts.app')

@section('title', 'Privacy Policy · '.$bnb['name'])
@section('meta_description', 'Informativa sulla privacy del B&B Cassino Centrale: come trattiamo i dati personali degli ospiti secondo il GDPR.')

@section('content')
    <section class="bg-cream-50 py-12">
        <div class="container-bnb">
            <span class="eyebrow">Informativa</span>
            <h1 class="section-title">Privacy Policy</h1>
            <p class="mt-2 text-sm text-ink-soft">Ultimo aggiornamento: {{ now()->translatedFormat('F Y') }}</p>
        </div>
    </section>

    <section class="container-bnb prose-bnb py-12">
        <p>La presente informativa descrive come il <strong>B&amp;B Cassino Centrale</strong> tratta i dati personali
        degli utenti che visitano questo sito e di chi ci contatta o effettua una prenotazione, in conformità al
        Regolamento (UE) 2016/679 (“GDPR”).</p>

        <h2>Titolare del trattamento</h2>
        <p>
            B&amp;B Cassino Centrale — {{ $bnb['contact']['address'] }}<br>
            Telefono: {{ $bnb['contact']['phone'] }} · Email:
            <a href="mailto:{{ $bnb['contact']['email'] }}">{{ $bnb['contact']['email'] }}</a><br>
            <em>[Da completare con ragione sociale, P.IVA/C.F. e legale rappresentante.]</em>
        </p>

        <h2>Quali dati raccogliamo</h2>
        <ul>
            <li><strong>Dati che ci fornisci volontariamente:</strong> nome, cognome, email, telefono, date di soggiorno,
            numero di ospiti ed eventuali note, quando invii una richiesta di informazioni o una prenotazione.</li>
            <li><strong>Dati di navigazione:</strong> dati tecnici raccolti automaticamente (indirizzo IP, tipo di browser,
            pagine visitate) necessari al funzionamento e alla sicurezza del sito.</li>
        </ul>

        <h2>Finalità e base giuridica</h2>
        <ul>
            <li>Gestire richieste di informazioni e prenotazioni (base giuridica: esecuzione di misure precontrattuali e
            del contratto).</li>
            <li>Adempiere agli obblighi di legge, anche fiscali e amministrativi.</li>
            <li>Garantire la sicurezza e il corretto funzionamento del sito (legittimo interesse).</li>
        </ul>

        <h2>Conservazione dei dati</h2>
        <p>I dati sono conservati per il tempo necessario alle finalità indicate e nel rispetto dei termini di legge
        (ad esempio gli obblighi fiscali). Trascorso tale periodo i dati vengono cancellati o resi anonimi.</p>

        <h2>Comunicazione dei dati</h2>
        <p>I dati non sono diffusi. Possono essere trattati da soggetti che ci forniscono servizi (es. hosting, gestione
        pagamenti, invio email/WhatsApp) nominati responsabili del trattamento, e comunicati alle autorità quando
        previsto dalla legge.</p>

        <h2>I tuoi diritti</h2>
        <p>Hai diritto di accedere ai tuoi dati, chiederne la rettifica o la cancellazione, limitarne o opporti al
        trattamento e richiederne la portabilità, oltre a proporre reclamo al Garante per la protezione dei dati
        personali. Per esercitare questi diritti scrivi a
        <a href="mailto:{{ $bnb['contact']['email'] }}">{{ $bnb['contact']['email'] }}</a>.</p>

        <h2>Cookie</h2>
        <p>Questo sito utilizza cookie: per i dettagli consulta la <a href="{{ route('legal.cookie') }}">Cookie Policy</a>.</p>

        <h2>Modifiche</h2>
        <p>Questa informativa può essere aggiornata nel tempo. Ti invitiamo a consultarla periodicamente.</p>

        <p class="text-sm text-ink-soft"><em>Documento di base fornito a titolo esemplificativo: ti consigliamo di farlo
        verificare per adeguarlo alla tua situazione specifica.</em></p>
    </section>
@endsection
