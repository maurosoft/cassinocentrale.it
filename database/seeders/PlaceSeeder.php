<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        // --- Luoghi turistici ---
        $attractions = [
            [
                'name' => 'Abbazia di Montecassino',
                'category' => 'religione',
                'description' => "Il celebre monastero fondato da San Benedetto nel 529, simbolo di Cassino. Distrutto durante la Seconda Guerra Mondiale e ricostruito fedelmente, offre una vista spettacolare sulla valle. Visita imperdibile per storia, arte e spiritualità.",
                'distance_walking' => null,
                'distance_car' => '20 min in auto',
                'distance_bus' => 'Navetta / bus dal centro',
                'lat' => 41.4917, 'lng' => 13.8144,
                'link' => 'https://www.abbaziamontecassino.org',
            ],
            [
                'name' => 'Parco Archeologico di Casinum',
                'category' => 'storia',
                'description' => "L'antica città romana di Casinum, con l'Anfiteatro, il Teatro Romano e il Mausoleo di Ummidia Quadratilla. Una passeggiata tra le rovine romane ai piedi di Montecassino.",
                'distance_walking' => '25 min a piedi',
                'distance_car' => '7 min in auto',
                'distance_bus' => 'Linea urbana',
                'lat' => 41.4836, 'lng' => 13.8203,
                'link' => null,
            ],
            [
                'name' => 'Historiale di Cassino',
                'category' => 'guerra',
                'description' => "Museo multimediale che racconta la Battaglia di Cassino del 1944. Un percorso emozionante tra filmati, ricostruzioni e testimonianze della Seconda Guerra Mondiale.",
                'distance_walking' => '10 min a piedi',
                'distance_car' => '4 min in auto',
                'distance_bus' => null,
                'lat' => 41.4869, 'lng' => 13.8339,
                'link' => null,
            ],
            [
                'name' => 'Cimitero di Guerra del Commonwealth',
                'category' => 'guerra',
                'description' => "Luogo della memoria che accoglie i caduti del Commonwealth durante la Battaglia di Cassino. Un sito di grande valore storico e commovente, immerso nel verde.",
                'distance_walking' => '20 min a piedi',
                'distance_car' => '6 min in auto',
                'distance_bus' => null,
                'lat' => 41.4794, 'lng' => 13.8286,
                'link' => null,
            ],
            [
                'name' => 'Rocca Janula',
                'category' => 'storia',
                'description' => "Fortezza medievale che domina Cassino dall'alto. Dopo il restauro è tornata visitabile e regala scorci panoramici sulla città e sull'abbazia.",
                'distance_walking' => '20 min a piedi (in salita)',
                'distance_car' => '8 min in auto',
                'distance_bus' => null,
                'lat' => 41.4906, 'lng' => 13.8256,
                'link' => null,
            ],
            [
                'name' => 'Sorgenti del Gari (Peschiera)',
                'category' => 'natura',
                'description' => "Un'oasi naturale con acque cristalline a pochi minuti dal centro: ideale per una passeggiata rilassante a contatto con la natura.",
                'distance_walking' => '15 min a piedi',
                'distance_car' => '5 min in auto',
                'distance_bus' => null,
                'lat' => 41.4778, 'lng' => 13.8425,
                'link' => null,
            ],
        ];

        foreach ($attractions as $i => $data) {
            Place::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                array_merge($data, [
                    'is_convention' => false,
                    'is_active' => true,
                    'sort_order' => $i,
                ]),
            );
        }

        // --- Negozi e attività locali (con convenzioni) ---
        $conventions = [
            [
                'name' => 'Ristorante da esempio',
                'category' => 'servizi',
                'type' => 'ristorante',
                'description' => 'Cucina tipica ciociara a pochi passi dal B&B.',
                'convention_description' => 'Sconto del 10% sul conto per i nostri ospiti, mostrando la conferma di prenotazione.',
                'distance_walking' => '3 min a piedi',
            ],
            [
                'name' => 'Caffetteria del Corso',
                'category' => 'servizi',
                'type' => 'bar',
                'description' => 'Bar storico per colazioni e aperitivi.',
                'convention_description' => 'Caffè omaggio con la colazione per gli ospiti del B&B.',
                'distance_walking' => '2 min a piedi',
            ],
            [
                'name' => 'Bottega dei sapori',
                'category' => 'servizi',
                'type' => 'negozio',
                'description' => 'Prodotti tipici locali e souvenir enogastronomici.',
                'convention_description' => 'Sconto del 5% sugli acquisti per i nostri ospiti.',
                'distance_walking' => '5 min a piedi',
            ],
            [
                'name' => 'Pizzeria del Centro',
                'category' => 'servizi',
                'type' => 'ristorante',
                'description' => 'Pizza cotta a legna, forno tradizionale nel cuore di Cassino.',
                'convention_description' => 'Bibita in omaggio per gli ospiti del B&B.',
                'distance_walking' => '4 min a piedi',
            ],
            [
                'name' => 'Gelateria Artigianale',
                'category' => 'servizi',
                'type' => 'bar',
                'description' => 'Gelato artigianale con gusti di stagione.',
                'convention_description' => 'Cono/coppetta piccola a prezzo speciale per i nostri ospiti.',
                'distance_walking' => '6 min a piedi',
            ],
            [
                'name' => 'Boutique Moda Cassino',
                'category' => 'servizi',
                'type' => 'negozio',
                'description' => 'Abbigliamento e accessori nel centro cittadino.',
                'convention_description' => 'Sconto del 10% sul primo acquisto per gli ospiti del B&B.',
                'distance_walking' => '5 min a piedi',
            ],
        ];

        foreach ($conventions as $i => $data) {
            Place::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($data['name'])],
                array_merge($data, [
                    'is_convention' => true,
                    'is_active' => true,
                    'sort_order' => $i,
                ]),
            );
        }
    }
}
