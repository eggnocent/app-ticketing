<?php

namespace App\Modules\TicketModule\Database\Factories;

use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'event_id' => null,
            'name' => fake()->randomElement(['Reguler', 'VIP', 'Presale']),
            'price_rupiah' => 100000,
            'initial_quota' => 50,
            'remaining_quota' => 50,
            'sales_start' => now()->subDay(),
            'sales_end' => now()->addDays(30),
            'status' => 'active',
        ];
    }

    public function forEvent(string $eventId): static
    {
        return $this->state(fn (array $attributes) => [
            'event_id' => $eventId,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
