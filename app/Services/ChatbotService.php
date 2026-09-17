<?php

namespace App\Services;

use App\Models\ChatbotLog;
use App\Models\Place;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Services\Chatbot\Providers;
use App\Support\Settings;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Cervello di "Zap", l'assistente del B&B.
 * Legge la configurazione da site_settings, costruisce la conoscenza
 * (camere, prezzi, luoghi) e parla col provider AI scelto.
 */
class ChatbotService
{
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

        $messages = array_merge($history, [['role' => 'user', 'content' => $message]]);
        $result = $instance->chat($this->systemPrompt(), $messages, $model);

        ChatbotLog::create([
            'session_id' => $sessionId,
            'question' => Str::limit($message, 2000, ''),
            'answer' => $result['ok'] ? Str::limit($result['reply'] ?? '', 4000, '') : null,
            'provider' => $provider,
            'model' => $model,
            'ok' => (bool) $result['ok'],
            'error' => $result['ok'] ? null : Str::limit($result['error'] ?? '', 490, ''),
        ]);

        return $result['ok'] ? $result['reply'] : $this->fallbackMessage();
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
