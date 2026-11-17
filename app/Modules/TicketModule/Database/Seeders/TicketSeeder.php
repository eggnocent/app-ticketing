<?php

namespace App\Modules\TicketModule\Database\Seeders;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $events = collect(app(EventInfoContract::class)->getEventOptions())->keyBy('title');

        $tickets = [
            ['event' => 'Konser Senja Nusantara', 'name' => 'Reguler', 'price' => 150000, 'quota' => 100],
            ['event' => 'Konser Senja Nusantara', 'name' => 'VIP', 'price' => 350000, 'quota' => 20],
            ['event' => 'Konser Senja Nusantara', 'name' => 'Tiket Habis', 'price' => 100000, 'quota' => 0],
            ['event' => 'Startup Tech Summit 2026', 'name' => 'Reguler', 'price' => 200000, 'quota' => 50],
            ['event' => 'Startup Tech Summit 2026', 'name' => 'Tiket Terakhir', 'price' => 250000, 'quota' => 1],
            ['event' => 'Lari Pagi Sehat', 'name' => '5K', 'price' => 75000, 'quota' => 200],
            ['event' => 'Workshop UI/UX Dasar', 'name' => 'Presale', 'price' => 100000, 'quota' => 30],
        ];

        foreach ($tickets as $ticket) {
            $event = $events->get($ticket['event']);

            if (! $event) {
                continue;
            }

            Ticket::updateOrCreate(
                ['event_id' => $event['id'], 'name' => $ticket['name']],
                [
                    'price_rupiah' => $ticket['price'],
                    'initial_quota' => $ticket['quota'],
                    'remaining_quota' => $ticket['quota'],
                    'sales_start' => now()->subDay(),
                    'sales_end' => now()->addDays(60),
                    'status' => 'active',
                ],
            );
        }
    }
}
