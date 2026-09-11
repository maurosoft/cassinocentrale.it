<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'guest_name' => 'Marco R.',
                'rating' => 5,
                'title' => 'Posizione perfetta',
                'comment' => 'A due passi dalla stazione, camere pulitissime e colazione ottima. Accoglienza gentilissima!',
                'source' => 'Google',
                'stay_date' => '2026-06-15',
            ],
            [
                'guest_name' => 'Giulia B.',
                'rating' => 5,
                'title' => 'Tornerò sicuramente',
                'comment' => 'Tutto curato nei dettagli, letto comodissimo e proprietarie disponibili. Consigliato per visitare Montecassino.',
                'source' => 'Booking',
                'stay_date' => '2026-05-02',
            ],
            [
                'guest_name' => 'Andreas K.',
                'rating' => 5,
                'title' => 'Great central B&B',
                'comment' => 'Clean, quiet and perfectly located. Wonderful breakfast and very kind hosts. Highly recommended.',
                'source' => 'Google',
                'stay_date' => '2026-04-20',
            ],
        ];

        foreach ($reviews as $i => $data) {
            Review::updateOrCreate(
                ['guest_name' => $data['guest_name'], 'stay_date' => $data['stay_date']],
                array_merge($data, ['is_visible' => true, 'sort_order' => $i]),
            );
        }
    }
}
