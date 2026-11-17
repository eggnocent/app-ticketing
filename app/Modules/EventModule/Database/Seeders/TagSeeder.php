<?php

namespace App\Modules\EventModule\Database\Seeders;

use App\Modules\EventModule\Models\Event;
use App\Modules\EventModule\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Musik', 'slug' => 'musik'],
            ['name' => 'Teknologi', 'slug' => 'teknologi'],
            ['name' => 'Olahraga', 'slug' => 'olahraga'],
            ['name' => 'Edukasi', 'slug' => 'edukasi'],
            ['name' => 'Hiburan', 'slug' => 'hiburan'],
            ['name' => 'Komunitas', 'slug' => 'komunitas'],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }

        $tagIds = Tag::query()->pluck('id', 'slug');
        $eventTags = [
            'konser-senja-nusantara' => ['musik', 'hiburan'],
            'startup-tech-summit-2026' => ['teknologi', 'edukasi'],
            'lari-pagi-sehat' => ['olahraga', 'komunitas'],
            'workshop-ui-ux-dasar' => ['teknologi', 'edukasi'],
            'festival-musik-batal' => ['musik', 'hiburan'],
        ];

        foreach ($eventTags as $eventSlug => $slugs) {
            $event = Event::query()->where('slug', $eventSlug)->first();

            if (! $event) {
                continue;
            }

            $event->tags()->sync(
                collect($slugs)->map(fn (string $slug) => $tagIds[$slug])->all(),
            );
        }
    }
}
