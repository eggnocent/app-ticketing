<?php

namespace App\Modules\EventModule\Services;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\EventModule\Models\Event;

class EventInfoService implements EventInfoContract
{
    public function getSummary(string $eventId): ?array
    {
        $event = Event::query()->with('category:id,name')->find($eventId);

        if (! $event) {
            return null;
        }

        return [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'category' => $event->category?->name,
            'location' => $event->location,
            'start_time' => $event->start_time?->toIso8601String(),
            'end_time' => $event->end_time?->toIso8601String(),
            'status' => $event->status,
        ];
    }

    public function isPurchasable(string $eventId): bool
    {
        return Event::query()
            ->whereKey($eventId)
            ->where('status', 'published')
            ->where('end_time', '>', now())
            ->exists();
    }
}
