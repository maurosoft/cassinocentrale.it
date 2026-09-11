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

    public function index(): View
    {
        $groups = SiteSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        $labels = self::GROUP_LABELS;

        return view('admin.settings.index', compact('groups', 'labels'));
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
