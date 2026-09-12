@extends('layouts.app')

@section('title', 'Cookie Policy · '.$bnb['name'])
@section('meta_description', 'Cookie Policy del B&B Cassino Centrale: quali cookie usiamo e come gestire le tue preferenze.')

@section('content')
    <section class="bg-cream-50 py-12">
        <div class="container-bnb">
            <span class="eyebrow">Informativa</span>
            <h1 class="section-title">Cookie Policy</h1>
            <p class="mt-2 text-sm text-ink-soft">Ultimo aggiornamento: {{ now()->translatedFormat('F Y') }}</p>
        </div>
    </section>

    <section class="container-bnb prose-bnb py-12">
        <p>I cookie sono piccoli file di testo che i siti salvano sul dispositivo dell’utente per farlo funzionare
        correttamente o per raccogliere informazioni. Di seguito i cookie utilizzati da questo sito.</p>

        <h2>Cookie tecnici (necessari)</h2>
        <p>Servono al funzionamento del sito e non richiedono consenso. Ad esempio:</p>
        <ul>
            <li>cookie di <strong>sessione</strong> e di <strong>sicurezza</strong> (protezione dei moduli, mantenimento
            della sessione dell’area riservata);</li>
            <li>cookie che memorizzano la tua <strong>preferenza sui cookie</strong> (per non mostrarti più il banner).</li>
        </ul>

        <h2>Cookie e servizi di terze parti</h2>
        <p>Alcune pagine possono includere contenuti di terze parti che utilizzano cookie o strumenti simili:</p>
        <ul>
            <li><strong>Google Maps</strong> (mappa nella pagina Contatti), per mostrare la nostra posizione;</li>
            <li><strong>Google Fonts</strong>, per i caratteri tipografici del sito.</li>
        </ul>
        <p>Questi servizi sono forniti da Google; per maggiori informazioni consulta le relative informative privacy.</p>

        <h2>Come gestire i cookie</h2>
        <p>Puoi gestire o disabilitare i cookie dalle impostazioni del tuo browser. La disattivazione di alcuni cookie
        potrebbe però compromettere il corretto funzionamento del sito.</p>

        <h2>Ulteriori informazioni</h2>
        <p>Per il trattamento dei dati personali consulta la nostra
        <a href="{{ route('legal.privacy') }}">Privacy Policy</a>. Per domande scrivi a
        <a href="mailto:{{ $bnb['contact']['email'] }}">{{ $bnb['contact']['email'] }}</a>.</p>

        <p class="text-sm text-ink-soft"><em>Documento di base fornito a titolo esemplificativo: se in futuro aggiungeremo
        strumenti di statistica o marketing (es. Google Analytics, pixel dei social), questa policy andrà aggiornata e il
        banner dovrà consentire la scelta puntuale dei consensi.</em></p>
    </section>
@endsection
