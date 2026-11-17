<?php

namespace App\Modules\EventModule\Database\Seeders;

use App\Modules\EventModule\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Musik', 'slug' => 'musik', 'description' => 'Konser dan pertunjukan musik.'],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Seminar, workshop, dan konferensi teknologi.'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'description' => 'Kegiatan dan kompetisi olahraga.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
