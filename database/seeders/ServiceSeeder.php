<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Wi-Fi in fibra', 'icon' => 'wifi', 'description' => 'Connessione veloce gratuita in tutta la struttura.'],
            ['name' => 'Colazione inclusa', 'icon' => 'coffee', 'description' => 'Colazione dolce e salata compresa nel prezzo.'],
            ['name' => 'Aria condizionata', 'icon' => 'snowflake', 'description' => 'Climatizzatore regolabile in camera.'],
            ['name' => 'TV', 'icon' => 'tv', 'description' => 'Televisore a schermo piatto.'],
            ['name' => 'Bagno privato', 'icon' => 'bath', 'description' => 'Bagno riservato con doccia e set di cortesia.'],
            ['name' => 'Angolo cottura', 'icon' => 'kitchen', 'description' => 'Piccola base cucina (solo in alcune camere).'],
            ['name' => 'Asciugacapelli', 'icon' => 'wind', 'description' => 'Disponibile in ogni bagno.'],
            ['name' => 'Biancheria e asciugamani', 'icon' => 'bed', 'description' => 'Lenzuola e asciugamani forniti e cambiati.'],
        ];

        foreach ($services as $i => $data) {
            Service::updateOrCreate(
                ['name' => $data['name']],
                [
                    'icon' => $data['icon'],
                    'description' => $data['description'],
                    'is_extra_cost' => false,
                    'amount' => 0,
                    'sort_order' => $i,
                ],
            );
        }
    }
}
