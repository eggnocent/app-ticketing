<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.test',
        ]);

        User::factory()->create([
            'name' => 'Peserta Satu',
            'email' => 'peserta1@example.test',
        ]);

        User::factory()->create([
            'name' => 'Peserta Dua',
            'email' => 'peserta2@example.test',
        ]);
    }
}
