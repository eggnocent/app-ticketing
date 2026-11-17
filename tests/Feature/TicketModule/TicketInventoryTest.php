<?php

namespace Tests\Feature\TicketModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event;
use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use App\Modules\TicketModule\Exceptions\TicketNotAvailableException;
use App\Modules\TicketModule\Exceptions\TicketQuotaExceededException;
use App\Modules\TicketModule\Models\Ticket;
use App\Modules\TicketModule\Services\TicketInventoryService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;
use Tests\TestCase;

class TicketInventoryTest extends TestCase
{
    use DatabaseMigrations;

    private function contract(): TicketInventoryContract
    {
        return app(TicketInventoryContract::class);
    }

    public function test_contract_is_bound_to_the_ticket_service(): void
    {
        $this->assertInstanceOf(TicketInventoryService::class, $this->contract());
    }

    public function test_event_tickets_only_include_active_tickets_ordered_by_price(): void
    {
        $event = Event::factory()->create();

        $expensive = Ticket::factory()->forEvent($event->id)->create(['name' => 'VIP', 'price_rupiah' => 300000]);
        $cheap = Ticket::factory()->forEvent($event->id)->create(['name' => 'Reguler', 'price_rupiah' => 100000]);
        Ticket::factory()->forEvent($event->id)->inactive()->create(['name' => 'Nonaktif']);

        $tickets = $this->contract()->getEventTickets($event->id);

        $this->assertCount(2, $tickets);
        $this->assertSame([$cheap->id, $expensive->id], array_column($tickets, 'id'));
    }

    public function test_summary_returns_primitive_data_and_null_for_unknown_ticket(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create();

        $summary = $this->contract()->getSummary($ticket->id);

        $this->assertSame($ticket->id, $summary['id']);
        $this->assertSame($ticket->price_rupiah, $summary['price_rupiah']);
        $this->assertTrue($summary['is_on_sale']);
        $this->assertNull($this->contract()->getSummary(Str::uuid()->toString()));
    }

    public function test_reserve_reduces_quota_and_audits_the_actor(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 10,
        ]);

        $this->actingAs($admin);

        $reserved = DB::transaction(fn () => $this->contract()->reserve($ticket->id, 3));

        $this->assertSame(3, $reserved['quantity']);
        $this->assertSame(100000, $reserved['price_rupiah']);
        $this->assertSame(7, $ticket->refresh()->remaining_quota);
        $this->assertSame($admin->id, $ticket->updated_by);
    }

    public function test_reserve_requires_an_active_database_transaction(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create();

        $this->expectException(LogicException::class);

        $this->contract()->reserve($ticket->id, 1);
    }

    public function test_reserve_rejects_inactive_ticket(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->inactive()->create();

        $this->expectException(TicketNotAvailableException::class);

        DB::transaction(fn () => $this->contract()->reserve($ticket->id, 1));
    }

    public function test_reserve_rejects_ticket_outside_the_sales_window(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'sales_start' => now()->addDay(),
            'sales_end' => now()->addDays(2),
        ]);

        $this->expectException(TicketNotAvailableException::class);

        DB::transaction(fn () => $this->contract()->reserve($ticket->id, 1));
    }

    public function test_reserve_rolls_back_when_quota_is_insufficient(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 5,
            'remaining_quota' => 5,
        ]);

        try {
            DB::transaction(fn () => $this->contract()->reserve($ticket->id, 6));
            $this->fail('Reserve seharusnya gagal.');
        } catch (TicketQuotaExceededException) {
            $this->assertSame(5, $ticket->refresh()->remaining_quota);
        }
    }

    public function test_release_restores_quota_and_never_exceeds_initial_quota(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 3,
        ]);

        DB::transaction(fn () => $this->contract()->release($ticket->id, 4));
        $this->assertSame(7, $ticket->refresh()->remaining_quota);

        DB::transaction(fn () => $this->contract()->release($ticket->id, 100));
        $this->assertSame(10, $ticket->refresh()->remaining_quota);
    }
}
