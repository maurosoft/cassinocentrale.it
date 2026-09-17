<?php

namespace App\Services;

use App\Models\ChatbotLog;
use App\Models\Place;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Services\Chatbot\Providers;
use App\Support\Settings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Cervello di "Zap", l'assistente del B&B.
 * Legge la configurazione da site_settings, costruisce la conoscenza
 * (camere, prezzi, luoghi) e parla col provider AI scelto.
 */
class ChatbotService
{
    /** Numero massimo di "giri" interni (controllo disponibilità / prenotazione) per messaggio. */
    private const MAX_TOOL_LOOPS = 4;

    public function __construct(
        private readonly ChatbotBookingTools $tools,
    ) {}

    public function name(): string
    {
        return Settings::get('chatbot.name') ?: 'Zap';
    }

    public function greeting(): string
    {
        return Settings::get('chatbot.greeting')
            ?: 'Ciao! Sono '.$this->name().', l’assistente del B&B Cassino Centrale. Come posso aiutarti? 😊';
    }

    public function provider(): string
    {
        return Settings::get('chatbot.provider', 'deepseek');
    }

    public function model(): string
    {
        return (string) Settings::get('chatbot.model', '');
    }

    /** Chiave API salvata per un provider (decifrata). Vuota se assente. */
    public function apiKey(?string $provider = null): string
    {
        $provider ??= $this->provider();
        $stored = Settings::get('chatbot.key_'.$provider);

        if (! $stored) {
            return '';
        }

        try {
            return Crypt::decryptString($stored);
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function hasKey(string $provider): bool
    {
        return $this->apiKey($provider) !== '';
    }

    /** Vero se il chatbot è acceso E configurato (provider + modello + chiave). */
    public function enabled(): bool
    {
        return (bool) Settings::get('chatbot.enabled', false)
            && Providers::exists($this->provider())
            && $this->model() !== ''
            && $this->hasKey($this->provider());
    }

    /** Vero se Zap può controllare disponibilità e prendere prenotazioni in chat. */
    public function bookingEnabled(): bool
    {
        return (bool) Settings::get('chatbot.booking_enabled', false);
    }

    /**
     * Rileva i modelli disponibili per un provider, usando la chiave data
     * oppure quella già salvata.
     *
     * @return array{ok: bool, models: array, error: ?string}
     */
    public function detectModels(string $provider, string $apiKey = ''): array
    {
        if (! Providers::exists($provider)) {
            return ['ok' => false, 'models' => [], 'error' => 'Provider non valido.'];
        }

        $key = $apiKey !== '' ? $apiKey : $this->apiKey($provider);

        if ($key === '') {
            return ['ok' => false, 'models' => [], 'error' => 'Inserisci prima la chiave API di questo provider.'];
        }

        $instance = Providers::make($provider, $key);
        $models = $instance ? $instance->models() : [];

        if (empty($models)) {
            return ['ok' => false, 'models' => [], 'error' => 'Nessun modello rilevato: controlla che la chiave sia corretta.'];
        }

        return ['ok' => true, 'models' => $models, 'error' => null];
    }

    /**
     * Risponde a un messaggio dell'utente.
     *
     * @param  array  $history  [['role'=>'user'|'assistant','content'=>string], ...]
     */
    public function reply(?string $sessionId, string $message, array $history = []): string
    {
        $provider = $this->provider();
        $model = $this->model();
        $instance = Providers::make($provider, $this->apiKey($provider));

        if (! $instance || $model === '') {
            return $this->fallbackMessage();
        }

        $system = $this->systemPrompt();
        $messages = array_merge($history, [['role' => 'user', 'content' => $message]]);

        $final = null;
        $error = null;

        // Ciclo: se Zap chiede un controllo disponibilità o una prenotazione,
        // eseguiamo lo strumento e gli restituiamo il risultato, poi continua.
        for ($i = 0; $i < self::MAX_TOOL_LOOPS; $i++) {
            $result = $instance->chat($system, $messages, $model);

            if (! $result['ok']) {
                $error = $result['error'] ?? 'errore sconosciuto';
                break;
            }

            $text = (string) $result['reply'];
            $action = $this->extractAction($text);

            if (! $action) {
                $final = $this->stripMarkers($text);
                break;
            }

            // Zap ha invocato uno strumento: registriamo il suo turno e la risposta del sistema.
            $messages[] = ['role' => 'assistant', 'content' => $text];
            $messages[] = ['role' => 'user', 'content' => '[SISTEMA] '.$this->runTool($action)];
        }

        if ($final === null && $error === null) {
            // Ha esaurito i giri restando su un comando: chiudiamo con garbo.
            $final = 'Fammi ricontrollare un attimo… puoi ripetermi le date e il numero di ospiti, per favore?';
        }

        ChatbotLog::create([
            'session_id' => $sessionId,
            'question' => Str::limit($message, 2000, ''),
            'answer' => $final !== null ? Str::limit($final, 4000, '') : null,
            'provider' => $provider,
            'model' => $model,
            'ok' => $error === null,
            'error' => $error ? Str::limit($error, 490, '') : null,
        ]);

        return $final ?? $this->fallbackMessage();
    }

    /**
     * Cerca un comando @@CHECK {...}@@ o @@BOOK {...}@@ nel testo del modello.
     *
     * @return array{type:string, args:array}|null
     */
    private function extractAction(string $text): ?array
    {
        if (! preg_match('/@@(CHECK|BOOK)\s*(\{.*?\})\s*@@/s', $text, $m)) {
            return null;
        }

        $args = json_decode($m[2], true);

        return [
            'type' => strtoupper($m[1]),
            'args' => is_array($args) ? $args : ['__invalid' => true],
        ];
    }

    /** Esegue lo strumento richiesto e ritorna il testo-risultato per il modello. */
    private function runTool(array $action): string
    {
        if (($action['args']['__invalid'] ?? false)) {
            return 'Formato del comando non valido. Riprova con un JSON corretto (date AAAA-MM-GG).';
        }

        if (! $this->bookingEnabled()) {
            return 'Le prenotazioni e i controlli disponibilità via chat non sono attivi: invita l\'ospite a usare la pagina Prenota ('.url('/prenota').').';
        }

        return match ($action['type']) {
            'CHECK' => $this->tools->check($action['args']),
            'BOOK' => $this->tools->book($action['args']),
            default => 'Comando sconosciuto.',
        };
    }

    /** Rimuove eventuali comandi rimasti nel testo mostrato all'ospite. */
    private function stripMarkers(string $text): string
    {
        $clean = preg_replace('/@@(CHECK|BOOK)\s*\{.*?\}\s*@@/s', '', $text);

        return trim((string) $clean);
    }

    private function fallbackMessage(): string
    {
        $phone = config('bnb.contact.phone');
        $email = config('bnb.contact.email');

        return "Mi dispiace, in questo momento non riesco a rispondere. "
            ."Puoi chiamarci al {$phone} o scriverci a {$email}: ti risponderemo al più presto!";
    }

    /**
     * Costruisce le istruzioni di sistema: personalità + conoscenza reale del B&B.
     */
    public function systemPrompt(): string
    {
        $bnb = config('bnb');
        $extra = trim((string) Settings::get('chatbot.notes', ''));
        $name = $this->name();

        $lines = [];
        $lines[] = "Sei {$name}, l'assistente virtuale del B&B Cassino Centrale.";
        $lines[] = 'Data di oggi: '.Carbon::now()->format('d/m/Y').'. Usala per interpretare frasi come "questo weekend" o "domani".';
        $lines[] = 'Parli in modo cordiale, caloroso e conciso. Rispondi SEMPRE nella stessa lingua del cliente (di default in italiano).';
        $lines[] = 'Usa SOLTANTO le informazioni qui sotto. Se non conosci una risposta (es. disponibilità di date precise o prezzi non elencati), NON inventare: invita gentilmente a completare la richiesta di prenotazione online oppure a contattare la struttura.';
        $lines[] = 'Quando il cliente mostra interesse, invitalo con garbo a prenotare dalla pagina Prenota ('.url('/prenota').'). Non essere insistente.';
        $lines[] = 'Non dare consigli legali, medici o finanziari. Non promettere sconti non elencati.';
        $lines[] = '';
        $lines[] = '=== DATI DELLA STRUTTURA ===';
        $lines[] = 'Nome: '.$bnb['name'].' — '.$bnb['tagline'];
        $lines[] = 'Indirizzo: '.$bnb['contact']['address'];
        $lines[] = 'Telefono: '.$bnb['contact']['phone'].' · Email: '.$bnb['contact']['email'];
        $lines[] = 'Check-in: '.$bnb['checkin']['from'].'–'.$bnb['checkin']['to'].' · Check-out: entro le '.$bnb['checkout']['until'];
        $lines[] = 'Tutte le camere sono matrimoniali con bagno privato, aria condizionata, TV, Wi-Fi in fibra e colazione inclusa. Conduzione femminile, in pieno centro (vicino a stazione, pullman, banche).';
        $lines[] = '';
        $lines[] = '=== PREZZI E SCONTI ===';
        $single = (int) ($bnb['pricing']['single_price'] ?? 65);
        $dbl = (int) ($bnb['pricing']['double_discount_percent'] ?? 10);
        $lsMin = (int) ($bnb['pricing']['long_stay_min_nights'] ?? 3);
        $lsPct = (int) ($bnb['pricing']['long_stay_percent'] ?? 10);
        $secondRoom = (int) Settings::get('pricing.second_room_discount_percent', $bnb['pricing']['second_room_discount_percent'] ?? 15);
        $lines[] = "Prezzo base: {$single}€ a notte per 1 persona.";
        $lines[] = "In 2 persone: sconto del {$dbl}% (quindi ".number_format($single * (1 - $dbl / 100), 2, ',', '.')."€ a notte).";
        $lines[] = "Soggiorni lunghi (da {$lsMin} notti): ulteriore {$lsPct}%. Gli sconti NON si sommano: si applica il più conveniente.";
        $lines[] = "Per 3-4 ospiti servono 2 camere; la seconda camera ha uno sconto del {$secondRoom}%.";
        $lines[] = 'La colazione è sempre inclusa. I pagamenti si possono fare in struttura o online.';

        // Camere reali dal database.
        $rooms = Room::query()->where('is_active', true)->orderBy('sort_order')->orderBy('number_name')->get();
        if ($rooms->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '=== CAMERE ===';
            foreach ($rooms as $room) {
                $desc = Str::limit(strip_tags((string) ($room->short_description ?: $room->description)), 160, '');
                $kitchen = $room->has_kitchenette ? ', con angolo cottura' : '';
                $lines[] = 'Camera '.$room->number_name.($room->name ? ' “'.$room->name.'”' : '')
                    .': da '.number_format((float) $room->base_price, 0, ',', '.').'€/notte, max '.$room->max_guests.' ospiti'.$kitchen.'. '.$desc;
            }
        }

        // Luoghi e negozi (Scopri Cassino).
        $places = Place::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        if ($places->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '=== SCOPRI CASSINO (luoghi e attività vicine) ===';
            foreach ($places as $place) {
                $label = $place->is_convention ? $place->typeLabel() : $place->categoryLabel();
                $parts = [$place->name.' ('.$label.')'];
                if (! empty($place->address)) {
                    $parts[] = 'indirizzo: '.$place->address;
                }
                if (! empty($place->distance_walking)) {
                    $parts[] = 'a '.$place->distance_walking.' a piedi';
                }
                $d = Str::limit(strip_tags((string) $place->description), 120, '');
                if ($d !== '') {
                    $parts[] = $d;
                }
                $lines[] = '- '.implode(' · ', $parts);
            }
        }

        if ($extra !== '') {
            $lines[] = '';
            $lines[] = '=== NOTE AGGIUNTIVE DELLA STRUTTURA ===';
            $lines[] = $extra;
        }

        if ($this->bookingEnabled()) {
            $privacy = url('/privacy');
            $lines[] = '';
            $lines[] = '=== PRENOTAZIONI (puoi controllare la disponibilità e prenotare) ===';
            $lines[] = 'Hai due comandi speciali. Quando ti servono, scrivi SOLO il comando su una riga, senza altro testo attorno. Il sistema ti risponderà con una riga che inizia con [SISTEMA]; poi torna a parlare normalmente con l\'ospite usando quel risultato.';
            $lines[] = '1) Verificare disponibilità e prezzo reali:';
            $lines[] = '@@CHECK {"checkin":"AAAA-MM-GG","checkout":"AAAA-MM-GG","guests":NUMERO}@@';
            $lines[] = '2) Creare la prenotazione (SOLO dopo aver raccolto e confermato: date, ospiti, nome, cognome, email, telefono e consenso privacy):';
            $lines[] = '@@BOOK {"checkin":"AAAA-MM-GG","checkout":"AAAA-MM-GG","guests":NUMERO,"first_name":"...","last_name":"...","email":"...","phone":"...","notes":"eventuali richieste","consent":true}@@';
            $lines[] = 'Regole importanti:';
            $lines[] = '- NON dare mai disponibilità o prezzi a memoria: usa SEMPRE @@CHECK@@ prima di indicare date libere o cifre.';
            $lines[] = '- Il check-out è il giorno di partenza (in quella notte non si dorme).';
            $lines[] = '- Chiedi i dati mancanti uno alla volta, con gentilezza. Le camere ospitano max 2 persone: per 3-4 ospiti servono 2 camere (lo calcola il sistema).';
            $lines[] = '- Prima di @@BOOK@@ mostra un riepilogo (date, ospiti, totale) e chiedi conferma esplicita.';
            $lines[] = '- Chiedi SEMPRE il consenso privacy con una frase tipo: "Confermi di aver letto l\'informativa privacy ('.$privacy.') e autorizzi l\'uso dei tuoi dati per gestire la prenotazione?". Metti "consent":true solo se l\'ospite acconsente.';
            $lines[] = '- Non inventare MAI un codice prenotazione: comunicane uno solo se te lo fornisce il sistema. La prenotazione resta "in attesa di conferma" da parte della struttura.';
        }

        return implode("\n", $lines);
    }

    /** Salva (cifrata) la chiave API di un provider. */
    public function storeKey(string $provider, string $apiKey): void
    {
        SiteSetting::updateOrCreate(
            ['key' => 'chatbot.key_'.$provider],
            ['value' => Crypt::encryptString($apiKey), 'type' => 'string', 'group' => 'chatbot'],
        );
    }
}
