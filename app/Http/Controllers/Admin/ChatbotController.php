<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\SiteSetting;
use App\Services\Chatbot\Providers;
use App\Services\ChatbotService;
use App\Support\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index(ChatbotService $bot): View
    {
        $providers = Providers::ALL;
        $keyStatus = [];
        foreach ($providers as $key => $meta) {
            $keyStatus[$key] = $bot->hasKey($key);
        }

        $logs = ChatbotLog::query()->latest()->limit(50)->get();

        return view('admin.chatbot.index', [
            'providers' => $providers,
            'keyStatus' => $keyStatus,
            'current' => [
                'enabled' => (bool) Settings::get('chatbot.enabled', false),
                'provider' => Settings::get('chatbot.provider', 'deepseek'),
                'model' => (string) Settings::get('chatbot.model', ''),
                'name' => $bot->name(),
                'greeting' => $bot->greeting(),
                'notes' => (string) Settings::get('chatbot.notes', ''),
            ],
            'logs' => $logs,
        ]);
    }

    public function save(Request $request, ChatbotService $bot): RedirectResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'in:'.implode(',', array_keys(Providers::ALL))],
            'model' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:40'],
            'greeting' => ['nullable', 'string', 'max:300'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $set = fn (string $key, string $value, string $type = 'string') => SiteSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => 'chatbot'],
        );

        $set('chatbot.provider', $data['provider']);
        $set('chatbot.model', $data['model'] ?? '');
        $set('chatbot.name', $data['name'] ?? 'Zap');
        $set('chatbot.greeting', $data['greeting'] ?? '');
        $set('chatbot.notes', $data['notes'] ?? '');
        $set('chatbot.enabled', $request->boolean('enabled') ? '1' : '0', 'boolean');

        // La chiave API si aggiorna solo se inserita (vuoto = resta quella salvata).
        if (! empty($data['api_key'])) {
            $bot->storeKey($data['provider'], trim($data['api_key']));
        }

        return redirect()->route('admin.chatbot.index')->with('success', 'Impostazioni di Zap salvate.');
    }

    /** Interroga il provider per l'elenco dei modelli (bottone "Rileva modelli"). */
    public function detectModels(Request $request, ChatbotService $bot): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'in:'.implode(',', array_keys(Providers::ALL))],
            'api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $bot->detectModels($data['provider'], trim((string) ($data['api_key'] ?? '')));

        return response()->json($result);
    }

    /** Prova rapida: manda un messaggio a Zap e restituisce la risposta. */
    public function test(Request $request, ChatbotService $bot): JsonResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:500']]);

        if (! $bot->enabled()) {
            return response()->json(['ok' => false, 'reply' => 'Zap non è ancora attivo/configurato: salva provider, modello e chiave, e spunta "Attiva".']);
        }

        $reply = $bot->reply('admin-test', $data['message'], []);

        return response()->json(['ok' => true, 'reply' => $reply]);
    }
}
