<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Service;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Servizi comuni a tutte le camere.
        $commonServices = Service::whereIn('name', [
            'Wi-Fi in fibra',
            'Colazione inclusa',
            'Aria condizionata',
            'TV',
            'Bagno privato',
            'Asciugacapelli',
            'Biancheria e asciugamani',
        ])->pluck('id')->all();

        $kitchenService = Service::where('name', 'Angolo cottura')->value('id');

        $rooms = [
            [
                'number_name' => '102',
                'name' => 'Matrimoniale Dante',
                'short_description' => 'Accogliente camera matrimoniale al primo piano, luminosa e silenziosa.',
                'description' => "Camera matrimoniale elegante e confortevole, ideale per coppie e viaggiatori di lavoro. Arredata con gusto in tonalità chiare e calde, dispone di bagno privato, aria condizionata, TV e Wi-Fi in fibra. La posizione centralissima ti permette di raggiungere a piedi stazione, negozi e ristoranti.",
                'base_price' => 65,
                'max_guests' => 2,
                'has_kitchenette' => false,
                'images' => ['images/rooms/room-102.jpg'],
                'sort_order' => 1,
            ],
            [
                'number_name' => '103',
                'name' => 'Matrimoniale Centrale',
                'short_description' => 'Comoda matrimoniale nel cuore di Cassino, a due passi da tutto.',
                'description' => "Una camera matrimoniale pensata per farti sentire a casa: letto comodo, bagno privato con doccia, climatizzatore e tutti i comfort per un soggiorno rilassante. Perfetta per chi vuole visitare Cassino e l'Abbazia di Montecassino senza pensieri.",
                'base_price' => 65,
                'max_guests' => 2,
                'has_kitchenette' => false,
                'images' => ['images/rooms/room-103.jpg'],
                'sort_order' => 2,
            ],
            [
                'number_name' => '104',
                'name' => 'Matrimoniale Charme',
                'short_description' => 'Camera matrimoniale spaziosa con dettagli curati e atmosfera calda.',
                'description' => "La nostra camera più curata nei dettagli: ambiente accogliente, colori chiari e tutti i servizi inclusi. Bagno privato, aria condizionata, TV e Wi-Fi in fibra. Ideale per una fuga romantica o per una tappa comoda lungo il viaggio.",
                'base_price' => 65,
                'max_guests' => 2,
                'has_kitchenette' => false,
                'images' => ['images/rooms/room-104.jpg'],
                'sort_order' => 3,
            ],
            [
                'number_name' => '205',
                'name' => 'Matrimoniale con angolo cottura',
                'short_description' => 'Matrimoniale al secondo piano con piccola base cucina: comoda per soggiorni più lunghi.',
                'description' => "La soluzione perfetta per soggiorni più lunghi o per chi ama un pizzico di autonomia: oltre a tutti i comfort delle altre camere (bagno privato, aria condizionata, TV, Wi-Fi in fibra e colazione inclusa), questa camera dispone di un piccolo angolo cottura e di un balcone. Silenziosa e riservata, al secondo piano.",
                'base_price' => 65,
                'max_guests' => 2,
                'has_kitchenette' => true,
                'images' => ['images/rooms/room-205.jpg'],
                'sort_order' => 4,
            ],
        ];

        foreach ($rooms as $data) {
            $room = Room::updateOrCreate(
                ['number_name' => $data['number_name']],
                array_merge($data, ['is_active' => true]),
            );

            $services = $commonServices;
            if ($room->has_kitchenette && $kitchenService) {
                $services[] = $kitchenService;
            }

            // Collega i servizi senza costo extra (extra_cost = null).
            $room->services()->sync(
                collect($services)->mapWithKeys(fn ($id) => [$id => ['extra_cost' => null]])->all(),
            );
        }
    }
}
