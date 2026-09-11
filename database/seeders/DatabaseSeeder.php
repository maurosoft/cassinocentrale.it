<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSettingSeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            RoomSeeder::class,
            PlaceSeeder::class,
            ReviewSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
