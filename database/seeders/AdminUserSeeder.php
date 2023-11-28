<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Vincenzo',
            'email' => 'barbershop8805@gmail.com',
            'password' => bcrypt('Giochidicapelli212'),
            'phone' => '3407454912',
            'is_admin' => true, // Imposta questo utente come amministratore
        ]);
    }
}
