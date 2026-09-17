<?php

namespace App\Services\Chatbot;

/**
 * Contratto comune per i provider AI del chatbot.
 * Ogni provider sa: parlare (chat) e dire quali modelli ha (models).
 */
interface ChatProvider
{
    /**
     * Invia la conversazione e restituisce la risposta.
     *
     * @param  string  $system    Istruzioni di sistema (personalità + conoscenza del B&B).
     * @param  array   $messages  Storico: [['role'=>'user'|'assistant','content'=>string], ...].
     * @param  string  $model     Modello scelto.
     * @return array   ['ok'=>bool, 'reply'=>?string, 'error'=>?string]
     */
    public function chat(string $system, array $messages, string $model): array;

    /**
     * Elenco degli id dei modelli disponibili (per il menu a tendina in admin).
     *
     * @return array<int, string>
     */
    public function models(): array;
}
