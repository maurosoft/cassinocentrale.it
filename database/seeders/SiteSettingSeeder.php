<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // SEO
            ['key' => 'seo.title', 'value' => 'B&B Cassino Centrale — Nel Cuore della Città', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo.description', 'value' => 'B&B a conduzione femminile nel pieno centro di Cassino: camere eleganti, Wi-Fi in fibra, colazione inclusa. A pochi passi da stazione, negozi e Abbazia di Montecassino.', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'seo.keywords', 'value' => 'B&B Cassino, bed and breakfast Cassino centro, dormire a Cassino, Montecassino, camere Cassino', 'type' => 'string', 'group' => 'seo'],

            // Home
            ['key' => 'home.hero_title', 'value' => 'Soggiorna a Cassino Centrale e hai tutto a portata di mano', 'type' => 'string', 'group' => 'home'],
            ['key' => 'home.hero_subtitle', 'value' => 'Nel cuore della città, a pochi passi da stazione, pullman, taxi, banche e Chiesa Madre.', 'type' => 'string', 'group' => 'home'],
            ['key' => 'home.intro', 'value' => 'Un piccolo B&B di charme a conduzione femminile, dove sentirti coccolato e avere ogni comodità davvero vicina.', 'type' => 'string', 'group' => 'home'],

            // Offerta in evidenza (idea marketing)
            ['key' => 'home.offer_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'offerte'],
            ['key' => 'home.offer_title', 'value' => 'Viaggiate in due? 10% di sconto', 'type' => 'string', 'group' => 'offerte'],
            ['key' => 'home.offer_text', 'value' => 'Camera a 65 € a notte: prenotando direttamente con noi, in due hai il 10% di sconto. Nessuna commissione, sempre la miglior tariffa.', 'type' => 'string', 'group' => 'offerte'],

            // Scopri Cassino
            ['key' => 'discover.intro', 'value' => 'Dal B&B raggiungi a piedi o in pochi minuti storia, arte e natura: dall’Abbazia di Montecassino ai luoghi della memoria, fino alle attività convenzionate del centro.', 'type' => 'string', 'group' => 'discover'],

            // Interruttori di sezione
            ['key' => 'reviews.enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'generale'],
            ['key' => 'whatsapp.button_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'generale'],

            // Manutenzione
            ['key' => 'site.maintenance_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'manutenzione'],
            ['key' => 'site.maintenance_message', 'value' => '', 'type' => 'string', 'group' => 'manutenzione'],
        ];

        foreach ($settings as $data) {
            SiteSetting::updateOrCreate(['key' => $data['key']], $data);
        }
    }
}
