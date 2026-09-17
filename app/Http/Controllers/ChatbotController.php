<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    /** Riceve un messaggio dal widget sul sito e restituisce la risposta di Zap. */
    public function message(Request $request, ChatbotService $bot): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['nullable', 'string'],
            'history.*.content' => ['nullable', 'string'],
        ]);

        if (! $bot->enabled()) {
            return response()->json([
                'reply' => 'Il nostro assistente non è attivo in questo momento. Scrivici a '
                    .config('bnb.contact.email').' o chiama '.config('bnb.contact.phone').'.',
            ]);
        }

        // Ripuliamo lo storico ricevuto dal browser (solo ruoli validi, testo limitato).
        $history = collect($data['history'] ?? [])
            ->map(fn ($m) => [
                'role' => in_array(($m['role'] ?? ''), ['user', 'assistant'], true) ? $m['role'] : 'user',
                'content' => mb_substr((string) ($m['content'] ?? ''), 0, 1000),
            ])
            ->filter(fn ($m) => $m['content'] !== '')
            ->take(20)
            ->values()
            ->all();

        $reply = $bot->reply($request->session()->getId(), $data['message'], $history);

        return response()->json(['reply' => $reply]);
    }
}
