<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
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
    private const SPECIAL_GROUPS = ['manutenzione', 'branding', 'notifiche', 'prezzi'];

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
