<?php

namespace Tests\Feature\EventModule;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\EventModule\Models\Event;
use App\Modules\EventModule\Services\EventInfoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventInfoContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_is_bound_to_the_event_service(): void
    {
        $this->assertInstanceOf(EventInfoService::class, app(EventInfoContract::class));
    }

    public function test_summary_returns_primitive_event_data(): void
    {
        $event = Event::factory()->published()->create();

        $summary = app(EventInfoContract::class)->getSummary($event->id);

        $this->assertSame($event->id, $summary['id']);
        $this->assertSame($event->title, $summary['title']);
        $this->assertSame($event->slug, $summary['slug']);
        $this->assertSame($event->category->name, $summary['category']);
        $this->assertSame($event->status, $summary['status']);
    }

    public function test_summary_returns_null_for_unknown_event(): void
    {
        $this->assertNull(app(EventInfoContract::class)->getSummary(Str::uuid()->toString()));
    }

    public function test_published_future_event_is_purchasable(): void
    {
        $event = Event::factory()->published()->create();

        $this->assertTrue(app(EventInfoContract::class)->isPurchasable($event->id));
    }

    public function test_draft_and_cancelled_events_are_not_purchasable(): void
    {
        $draft = Event::factory()->draft()->create();
        $cancelled = Event::factory()->cancelled()->create();

        $contract = app(EventInfoContract::class);

        $this->assertFalse($contract->isPurchasable($draft->id));
        $this->assertFalse($contract->isPurchasable($cancelled->id));
    }

    public function test_ended_event_is_not_purchasable(): void
    {
        $event = Event::factory()->published()->create([
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDay(),
        ]);

        $this->assertFalse(app(EventInfoContract::class)->isPurchasable($event->id));
    }
}
