<?php

namespace Database\Seeders;

use App\Modules\EventModule\Database\Seeders\CategorySeeder;
use App\Modules\EventModule\Database\Seeders\EventSeeder;
use App\Modules\EventModule\Database\Seeders\TagSeeder;
use App\Modules\TicketModule\Database\Seeders\TicketSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            EventSeeder::class,
            TagSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
