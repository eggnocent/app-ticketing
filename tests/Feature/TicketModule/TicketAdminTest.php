<?php

namespace Tests\Feature\TicketModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAdminTest extends TestCase
{
    use RefreshDatabase;

    private function payload(string $eventId, array $overrides = []): array
    {
        return array_merge([
            'event_id' => $eventId,
            'name' => 'Reguler',
            'price_rupiah' => 150000,
            'initial_quota' => 40,
            'sales_start' => now()->subDay()->format('Y-m-d\TH:i'),
            'sales_end' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'status' => 'active',
        ], $overrides);
    }

    public function test_guest_is_redirected_from_admin_ticket_routes(): void
    {
        $this->get(route('admin.tickets.index'))->assertRedirect(route('login'));
    }

    public function test_participant_is_forbidden_from_admin_ticket_routes(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.tickets.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_ticket_and_the_actor_is_audited(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.tickets.store'), $this->payload($event->id))
            ->assertRedirect(route('admin.tickets.index'));

        $ticket = Ticket::query()->where('event_id', $event->id)->firstOrFail();

        $this->assertSame(40, $ticket->remaining_quota);
        $this->assertSame($admin->id, $ticket->created_by);
        $this->assertSame($admin->id, $ticket->updated_by);
    }

    public function test_sales_end_must_be_after_sales_start(): void
    {
        $event = Event::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.tickets.store'), $this->payload($event->id, [
                'sales_start' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'sales_end' => now()->addDay()->format('Y-m-d\TH:i'),
            ]))
            ->assertSessionHasErrors('sales_end');

        $this->assertDatabaseMissing('tickets', ['event_id' => $event->id]);
    }

    public function test_update_recalculates_remaining_quota_from_sold_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 7,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.tickets.update', $ticket), $this->payload($ticket->event_id, [
                'name' => 'Reguler Baru',
                'initial_quota' => 12,
            ]))
            ->assertRedirect(route('admin.tickets.index'));

        $ticket->refresh();

        $this->assertSame('Reguler Baru', $ticket->name);
        $this->assertSame(9, $ticket->remaining_quota);
        $this->assertSame($admin->id, $ticket->updated_by);
    }

    public function test_update_rejects_initial_quota_below_sold_tickets(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 7,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.tickets.update', $ticket), $this->payload($ticket->event_id, [
                'initial_quota' => 2,
            ]))
            ->assertSessionHasErrors('initial_quota');

        $this->assertSame(10, $ticket->refresh()->initial_quota);
    }

    public function test_admin_can_open_ticket_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create(['title' => 'Event Halaman Tiket']);
        $ticket = Ticket::factory()->forEvent($event->id)->create(['name' => 'Tiket Halaman']);

        $this->actingAs($admin)
            ->get(route('admin.tickets.index'))
            ->assertOk()
            ->assertSee('Event Halaman Tiket')
            ->assertSee('Tiket Halaman');

        $this->actingAs($admin)->get(route('admin.tickets.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.tickets.edit', $ticket))->assertOk();
    }

    public function test_update_rejects_moving_event_after_sales(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 9,
        ]);
        $otherEvent = Event::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.tickets.update', $ticket), $this->payload($otherEvent->id))
            ->assertSessionHasErrors('event_id');

        $this->assertSame($ticket->event_id, $ticket->refresh()->event_id);
    }

    public function test_admin_can_delete_an_unsold_ticket(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 10,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.tickets.destroy', $ticket))
            ->assertRedirect(route('admin.tickets.index'));

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_ticket_with_sales_cannot_be_deleted(): void
    {
        $ticket = Ticket::factory()->forEvent(Event::factory()->create()->id)->create([
            'initial_quota' => 10,
            'remaining_quota' => 9,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.tickets.destroy', $ticket))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }
}
