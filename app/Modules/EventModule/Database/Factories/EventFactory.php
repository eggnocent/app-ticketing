<?php

namespace App\Modules\EventModule\Database\Factories;

use App\Modules\EventModule\Models\Category;
use App\Modules\EventModule\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $startTime = fake()->dateTimeBetween('+1 day', '+1 month');
        $endTime = (clone $startTime)->modify('+2 hours');

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::uuid()),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'draft',
            'banner_path' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
