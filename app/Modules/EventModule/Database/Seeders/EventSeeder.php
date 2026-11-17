<?php

namespace App\Modules\EventModule\Database\Seeders;

use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()->pluck('id', 'slug');

        $events = [
            [
                'category' => 'musik',
                'title' => 'Konser Senja Nusantara',
                'slug' => 'konser-senja-nusantara',
                'description' => 'Konser musik tahunan yang menampilkan musisi nasional.',
                'location' => 'Jakarta',
                'start_time' => Carbon::now()->addDays(7)->setTime(19, 0),
                'end_time' => Carbon::now()->addDays(7)->setTime(22, 0),
                'status' => 'published',
            ],
            [
                'category' => 'teknologi',
                'title' => 'Startup Tech Summit 2026',
                'slug' => 'startup-tech-summit-2026',
                'description' => 'Konferensi teknologi dan startup dengan pembicara industri.',
                'location' => 'Bandung',
                'start_time' => Carbon::now()->addDays(14)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(14)->setTime(17, 0),
                'status' => 'published',
            ],
            [
                'category' => 'olahraga',
                'title' => 'Lari Pagi Sehat',
                'slug' => 'lari-pagi-sehat',
                'description' => 'Kegiatan lari pagi bersama komunitas.',
                'location' => 'Surabaya',
                'start_time' => Carbon::now()->addDays(21)->setTime(6, 0),
                'end_time' => Carbon::now()->addDays(21)->setTime(10, 0),
                'status' => 'published',
            ],
            [
                'category' => 'teknologi',
                'title' => 'Workshop UI/UX Dasar',
                'slug' => 'workshop-ui-ux-dasar',
                'description' => 'Workshop pengantar desain antarmuka dan pengalaman pengguna.',
                'location' => 'Yogyakarta',
                'start_time' => Carbon::now()->addDays(30)->setTime(13, 0),
                'end_time' => Carbon::now()->addDays(30)->setTime(16, 0),
                'status' => 'draft',
            ],
            [
                'category' => 'musik',
                'title' => 'Festival Musik Batal',
                'slug' => 'festival-musik-batal',
                'description' => 'Contoh event yang dibatalkan untuk pengujian filter katalog.',
                'location' => 'Semarang',
                'start_time' => Carbon::now()->addDays(10)->setTime(15, 0),
                'end_time' => Carbon::now()->addDays(10)->setTime(21, 0),
                'status' => 'cancelled',
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['slug' => $event['slug']],
                [
                    'category_id' => $categories[$event['category']],
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'location' => $event['location'],
                    'start_time' => $event['start_time'],
                    'end_time' => $event['end_time'],
                    'status' => $event['status'],
                ],
            );
        }
    }
}
