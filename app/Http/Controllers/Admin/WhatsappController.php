<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\WhatsappProvider;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function index(Request $request): View
    {
        $providers = WhatsappProvider::orderBy('order_fallback')->orderBy('id')->get();
        $editing = $request->query('edit') ? WhatsappProvider::find($request->query('edit')) : null;

        return view('admin.whatsapp.index', compact('providers', 'editing'));
    }

    /** Impostazioni globali (on/off e numero staff). */
    public function settings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admin_whatsapp' => ['nullable', 'string', 'max:30'],
        ]);

        SiteSetting::updateOrCreate(['key' => 'whatsapp.enabled'], ['value' => $request->boolean('enabled') ? '1' : '0', 'type' => 'boolean', 'group' => 'whatsapp']);
        SiteSetting::updateOrCreate(['key' => 'notify.admin_whatsapp'], ['value' => $data['admin_whatsapp'] ?? '', 'type' => 'string', 'group' => 'notifiche']);

        return redirect()->route('admin.whatsapp.index')->with('success', 'Impostazioni WhatsApp salvate.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, required: true);
        WhatsappProvider::create($data);

        return redirect()->route('admin.whatsapp.index')->with('success', 'Provider aggiunto.');
    }

    public function update(Request $request, WhatsappProvider $provider): RedirectResponse
    {
        $data = $this->validated($request, required: false);

        // Il token si aggiorna solo se inserito (altrimenti resta quello salvato).
        if (empty($data['token'])) {
            unset($data['token']);
        }

        $provider->update($data);

        return redirect()->route('admin.whatsapp.index')->with('success', 'Provider aggiornato.');
    }

    public function destroy(WhatsappProvider $provider): RedirectResponse
    {
        $provider->delete();

        return redirect()->route('admin.whatsapp.index')->with('success', 'Provider rimosso.');
    }

    /** Invia un messaggio di prova con un provider specifico. */
    public function test(Request $request, WhatsAppService $service): RedirectResponse
    {
        $data = $request->validate([
            'provider_id' => ['required', 'exists:whatsapp_providers,id'],
            'number' => ['required', 'string', 'max:30'],
        ]);

        $provider = WhatsappProvider::findOrFail($data['provider_id']);
        $result = $service->testSend($provider, $data['number'], 'Messaggio di test da B&B Cassino Centrale ✅');

        return redirect()->route('admin.whatsapp.index')->with(
            $result['ok'] ? 'success' : 'error',
            $result['ok'] ? 'Messaggio di test inviato!' : 'Invio non riuscito: '.($result['response'] ?? '').' (vedi Log WhatsApp).'
        );
    }

    private function validated(Request $request, bool $required): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'provider' => ['required', 'in:'.implode(',', array_keys(WhatsappProvider::PROVIDERS))],
            'base_url' => ['nullable', 'url', 'max:200'],
            'token' => [$required ? 'required' : 'nullable', 'string', 'max:500'],
            'instance_id' => ['nullable', 'string', 'max:150'],
            'sender' => ['nullable', 'string', 'max:50'],
            'timeout' => ['nullable', 'integer', 'min:3', 'max:60'],
            'order_fallback' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active'), 'timeout' => (int) $request->input('timeout', 15), 'order_fallback' => (int) $request->input('order_fallback', 0)];
    }
}
