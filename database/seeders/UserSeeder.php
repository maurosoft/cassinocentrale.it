<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Utenti di test per l'area admin (Fase 2).
        // ATTENZIONE: cambiare queste password prima di andare online!
        $users = [
            [
                'name' => 'Superadmin',
                'email' => 'admin@cassinocentrale.it',
                'role' => User::ROLE_SUPERADMIN,
            ],
            [
                'name' => 'Reception',
                'email' => 'reception@cassinocentrale.it',
                'role' => User::ROLE_RECEPTION,
            ],
            [
                'name' => 'Editor',
                'email' => 'editor@cassinocentrale.it',
                'role' => User::ROLE_EDITOR,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
