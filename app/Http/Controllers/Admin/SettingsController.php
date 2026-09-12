<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /** Etichette leggibili per i gruppi di impostazioni. */
    private const GROUP_LABELS = [
        'seo' => 'SEO (motori di ricerca)',
        'home' => 'Home',
        'offerte' => 'Offerta in evidenza',
        'discover' => 'Scopri Cassino',
        'generale' => 'Interruttori sezioni',
    ];

    /** Gruppi gestiti con schede dedicate (non nel form generico). */
    private const SPECIAL_GROUPS = ['manutenzione', 'branding', 'notifiche', 'prezzi', 'smtp', 'stripe'];

    public function index(): View
    {
        $groups = SiteSetting::orderBy('group')->orderBy('key')->get()
            ->reject(fn (SiteSetting $s) => in_array($s->group, self::SPECIAL_GROUPS, true))
            ->groupBy('group');
        $labels = self::GROUP_LABELS;

        return view('admin.settings.index', compact('groups', 'labels'));
    }

    /** Carica/aggiorna il logo del sito. */
    public function branding(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ], [
            'logo.required' => 'Scegli un file immagine per il logo.',
        ]);

        $path = $request->file('logo')->store('branding', 'public');

        SiteSetting::updateOrCreate(
            ['key' => 'branding.logo'],
            ['value' => 'storage/'.$path, 'type' => 'string', 'group' => 'branding'],
        );

        return redirect()->route('admin.settings.index')->with('success', 'Logo aggiornato.');
    }

    /** Salva la configurazione SMTP (email in uscita). */
    public function smtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'host' => ['nullable', 'string', 'max:150'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'username' => ['nullable', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'max:255'],
            'encryption' => ['nullable', 'in:tls,ssl,none'],
            'from_email' => ['nullable', 'email', 'max:150'],
            'from_name' => ['nullable', 'string', 'max:120'],
            'admin_email' => ['nullable', 'email', 'max:150'],
        ]);

        $values = [
            'smtp.host' => ['value' => $data['host'] ?? '', 'group' => 'smtp', 'type' => 'string'],
            'smtp.port' => ['value' => (string) ($data['port'] ?? 587), 'group' => 'smtp', 'type' => 'integer'],
            'smtp.username' => ['value' => $data['username'] ?? '', 'group' => 'smtp', 'type' => 'string'],
            'smtp.encryption' => ['value' => $data['encryption'] ?? 'tls', 'group' => 'smtp', 'type' => 'string'],
            'smtp.from_email' => ['value' => $data['from_email'] ?? '', 'group' => 'smtp', 'type' => 'string'],
            'smtp.from_name' => ['value' => $data['from_name'] ?? '', 'group' => 'smtp', 'type' => 'string'],
            'smtp.enabled' => ['value' => $request->boolean('enabled') ? '1' : '0', 'group' => 'smtp', 'type' => 'boolean'],
            'notify.admin_email' => ['value' => $data['admin_email'] ?? '', 'group' => 'notifiche', 'type' => 'string'],
        ];

        foreach ($values as $key => $v) {
            SiteSetting::updateOrCreate(['key' => $key], $v);
        }

        // La password si aggiorna solo se inserita (altrimenti resta quella salvata).
        if (! empty($data['password'])) {
            SiteSetting::updateOrCreate(['key' => 'smtp.password'], ['value' => $data['password'], 'type' => 'string', 'group' => 'smtp']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Impostazioni email salvate.');
    }

    /** Salva la configurazione Stripe (pagamenti online). */
    public function stripe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'public_key' => ['nullable', 'string', 'max:255'],
            'secret_key' => ['nullable', 'string', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'deposit_percent' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        SiteSetting::updateOrCreate(['key' => 'stripe.enabled'], ['value' => $request->boolean('enabled') ? '1' : '0', 'type' => 'boolean', 'group' => 'stripe']);
        SiteSetting::updateOrCreate(['key' => 'stripe.deposit_percent'], ['value' => (string) $data['deposit_percent'], 'type' => 'integer', 'group' => 'stripe']);
        SiteSetting::updateOrCreate(['key' => 'stripe.public_key'], ['value' => $data['public_key'] ?? '', 'type' => 'string', 'group' => 'stripe']);

        // Chiavi segrete: aggiornate solo se inserite (vuoto = invariate).
        if (! empty($data['secret_key'])) {
            SiteSetting::updateOrCreate(['key' => 'stripe.secret_key'], ['value' => $data['secret_key'], 'type' => 'string', 'group' => 'stripe']);
        }
        if (! empty($data['webhook_secret'])) {
            SiteSetting::updateOrCreate(['key' => 'stripe.webhook_secret'], ['value' => $data['webhook_secret'], 'type' => 'string', 'group' => 'stripe']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Impostazioni Stripe salvate.');
    }

    /** Invia un'email di prova. */
    public function testEmail(Request $request, NotificationService $notifier): RedirectResponse
    {
        $data = $request->validate(['test_email' => ['required', 'email']]);

        $ok = $notifier->sendTest($data['test_email']);

        return redirect()->route('admin.settings.index')->with(
            $ok ? 'success' : 'error',
            $ok
                ? 'Email di test inviata a '.$data['test_email'].' — controlla la casella.'
                : 'Invio non riuscito: verifica i dati SMTP. Il dettaglio dell\'errore è nei Log email.'
        );
    }

    /** Carica/aggiorna la favicon (icona nella scheda del browser). */
    public function favicon(Request $request): RedirectResponse
    {
        $request->validate([
            'favicon' => ['required', 'image', 'mimes:png,ico,svg,jpg,jpeg,webp', 'max:1024'],
        ], [
            'favicon.required' => 'Scegli un file per la favicon.',
        ]);

        $path = $request->file('favicon')->store('branding', 'public');

        SiteSetting::updateOrCreate(
            ['key' => 'branding.favicon'],
            ['value' => 'storage/'.$path, 'type' => 'string', 'group' => 'branding'],
        );

        return redirect()->route('admin.settings.index')->with('success', 'Favicon aggiornata.');
    }

    /** Salva lo sconto per la seconda camera (prenotazioni di gruppo). */
    public function pricing(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'second_room_discount_percent' => ['required', 'integer', 'min:0', 'max:90'],
        ], [], [
            'second_room_discount_percent' => 'sconto seconda camera',
        ]);

        SiteSetting::updateOrCreate(
            ['key' => 'pricing.second_room_discount_percent'],
            ['value' => (string) $data['second_room_discount_percent'], 'type' => 'integer', 'group' => 'prezzi'],
        );

        return redirect()->route('admin.settings.index')->with('success', 'Sconto seconda camera aggiornato.');
    }

    /** Salva gli interruttori delle notifiche (attive/da inviare). */
    public function notifications(Request $request): RedirectResponse
    {
        $toggles = [
            'notify.email_new', 'notify.whatsapp_new',
            'notify.email_change', 'notify.whatsapp_change',
            'notify.email_cancel', 'notify.whatsapp_cancel',
            'notify.reminder_enabled',
        ];

        foreach ($toggles as $key) {
            $field = str_replace('notify.', '', $key);
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->boolean($field) ? '1' : '0', 'type' => 'boolean', 'group' => 'notifiche'],
            );
        }

        SiteSetting::updateOrCreate(
            ['key' => 'notify.reminder_days'],
            ['value' => (string) max(0, (int) $request->input('reminder_days', 1)), 'type' => 'integer', 'group' => 'notifiche'],
        );

        return redirect()->route('admin.settings.index')->with('success', 'Impostazioni notifiche salvate.');
    }

    /** Accende/spegne la modalità manutenzione (solo superadmin). */
    public function maintenance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        SiteSetting::updateOrCreate(
            ['key' => 'site.maintenance_enabled'],
            ['value' => $request->boolean('maintenance_enabled') ? '1' : '0', 'type' => 'boolean', 'group' => 'manutenzione'],
        );

        SiteSetting::updateOrCreate(
            ['key' => 'site.maintenance_message'],
            ['value' => $data['maintenance_message'] ?? '', 'type' => 'string', 'group' => 'manutenzione'],
        );

        $on = $request->boolean('maintenance_enabled');

        return redirect()->route('admin.settings.index')
            ->with('success', $on ? 'Modalità manutenzione ATTIVATA: i visitatori vedono la pagina di cortesia.' : 'Modalità manutenzione disattivata: il sito è di nuovo pubblico.');
    }

    public function update(Request $request): RedirectResponse
    {
        $values = $request->input('settings', []);

        foreach (SiteSetting::all() as $setting) {
            if (in_array($setting->type, ['boolean', 'bool'], true)) {
                // Le caselle non spuntate non arrivano: interpretiamo la presenza.
                $setting->value = array_key_exists($setting->key, $values) ? '1' : '0';
            } elseif (array_key_exists($setting->key, $values)) {
                $setting->value = $values[$setting->key];
            }
            $setting->save();
        }

        return redirect()->route('admin.settings.index')->with('success', 'Impostazioni salvate.');
    }
}
