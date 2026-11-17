<?php

namespace Tests\Feature\EventModule;

use App\Modules\EventModule\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_only_published_events_in_the_catalog(): void
    {
        Event::factory()->published()->create(['title' => 'Event Publik']);
        Event::factory()->draft()->create(['title' => 'Event Draft']);
        Event::factory()->cancelled()->create(['title' => 'Event Batal']);

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee('Event Publik')
            ->assertDontSee('Event Draft')
            ->assertDontSee('Event Batal');
    }

    public function test_guest_can_open_a_published_event_detail(): void
    {
        $event = Event::factory()->published()->create(['title' => 'Konser Publik']);

        $this->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertSee('Konser Publik');
    }

    public function test_draft_event_detail_is_not_found(): void
    {
        $event = Event::factory()->draft()->create();

        $this->get(route('events.show', $event->slug))->assertNotFound();
    }

    public function test_cancelled_event_detail_is_not_found(): void
    {
        $event = Event::factory()->cancelled()->create();

        $this->get(route('events.show', $event->slug))->assertNotFound();
    }
}
