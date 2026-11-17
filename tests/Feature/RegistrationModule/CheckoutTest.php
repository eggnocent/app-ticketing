<?php

namespace Tests\Feature\RegistrationModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event as EventModel;
use App\Modules\RegistrationModule\Events\TicketPurchasedEvent;
use App\Modules\RegistrationModule\Models\IssuedTicket;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Models\RegistrationTicket;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use DatabaseMigrations;

    public function test_participant_can_checkout_single_ticket_type(): void
    {
        Event::fake([TicketPurchasedEvent::class]);

        $user = User::factory()->create();
        $event = EventModel::factory()->published()->create();
        $ticket = Ticket::factory()->forEvent($event->id)->create([
            'price_rupiah' => 100000,
            'initial_quota' => 5,
            'remaining_quota' => 5,
        ]);

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 2],
            ]);

        $registration = Registration::query()->sole();

        $response->assertRedirect(route('registrations.show', $registration));

        $this->assertSame(200000, $registration->total_rupiah);
        $this->assertSame('completed', $registration->status);
        $this->assertNotNull($registration->confirmed_at);
        $this->assertSame($user->id, $registration->created_by);
        $this->assertSame($user->id, $registration->updated_by);

        $line = RegistrationTicket::query()->sole();

        $this->assertSame($ticket->id, $line->ticket_id);
        $this->assertSame(2, $line->quantity);
        $this->assertSame(100000, $line->unit_price_rupiah);
        $this->assertSame(200000, $line->subtotal_rupiah);
        $this->assertSame($user->id, $line->created_by);

        $codes = IssuedTicket::query()->pluck('ticket_code');

        $this->assertCount(2, $codes);
        $this->assertCount(2, $codes->unique());

        $this->assertSame(3, $ticket->refresh()->remaining_quota);
        $this->assertSame($user->id, $ticket->updated_by);

        Event::assertDispatched(TicketPurchasedEvent::class);
    }

    public function test_checkout_rolls_back_when_a_second_item_has_no_quota(): void
    {
        $user = User::factory()->create();
        $event = EventModel::factory()->published()->create();

        $available = Ticket::factory()->forEvent($event->id)->create([
            'initial_quota' => 2,
            'remaining_quota' => 2,
        ]);

        $soldOut = Ticket::factory()->forEvent($event->id)->create([
            'name' => 'Sold Out',
            'initial_quota' => 0,
            'remaining_quota' => 0,
        ]);

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [
                    $available->id => 1,
                    $soldOut->id => 1,
                ],
            ]);

        $response->assertSessionHas('error');

        $this->assertSame(0, Registration::query()->count());
        $this->assertSame(0, RegistrationTicket::query()->count());
        $this->assertSame(0, IssuedTicket::query()->count());
        $this->assertSame(2, $available->refresh()->remaining_quota);
    }

    public function test_checkout_rejects_a_draft_event(): void
    {
        $user = User::factory()->create();
        $event = EventModel::factory()->draft()->create();
        $ticket = Ticket::factory()->forEvent($event->id)->create();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
            ])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Registration::query()->count());
        $this->assertSame($ticket->remaining_quota, $ticket->refresh()->remaining_quota);
    }

    public function test_checkout_rejects_tickets_from_different_events(): void
    {
        $user = User::factory()->create();

        $first = Ticket::factory()->forEvent(EventModel::factory()->published()->create()->id)->create();
        $second = Ticket::factory()->forEvent(EventModel::factory()->published()->create()->id)->create();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    ['ticket_id' => $first->id, 'quantity' => 1],
                    ['ticket_id' => $second->id, 'quantity' => 1],
                ],
            ])
            ->assertSessionHasErrors('items');

        $this->assertSame(0, Registration::query()->count());
    }

    public function test_checkout_rejects_duplicate_ticket_ids(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->forEvent(EventModel::factory()->published()->create()->id)->create();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    ['ticket_id' => $ticket->id, 'quantity' => 1],
                    ['ticket_id' => $ticket->id, 'quantity' => 1],
                ],
            ])
            ->assertSessionHasErrors('items.1.ticket_id');

        $this->assertSame(0, Registration::query()->count());
    }

    public function test_checkout_validates_quantity_bounds(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->forEvent(EventModel::factory()->published()->create()->id)->create();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    ['ticket_id' => $ticket->id, 'quantity' => 0],
                ],
            ])
            ->assertSessionHasErrors('items.0.quantity');

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    ['ticket_id' => $ticket->id, 'quantity' => 11],
                ],
            ])
            ->assertSessionHasErrors('items.0.quantity');

        $this->assertSame(0, Registration::query()->count());
    }

    public function test_checkout_requires_an_existing_ticket(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'items' => [
                    ['ticket_id' => '00000000-0000-0000-0000-000000000000', 'quantity' => 1],
                ],
            ])
            ->assertSessionHasErrors('items.0.ticket_id');

        $this->assertSame(0, Registration::query()->count());
    }

    public function test_checkout_requires_authentication(): void
    {
        $this->post(route('checkout.store'))->assertRedirect(route('login'));
    }

    public function test_ticket_code_column_rejects_duplicates(): void
    {
        $ticket = Ticket::factory()->forEvent(EventModel::factory()->create()->id)->create();

        $line = RegistrationTicket::factory()->create(['ticket_id' => $ticket->id]);

        IssuedTicket::create([
            'registration_ticket_id' => $line->id,
            'ticket_code' => 'TKT-DUPLICATE',
        ]);

        $this->expectException(QueryException::class);

        IssuedTicket::create([
            'registration_ticket_id' => $line->id,
            'ticket_code' => 'TKT-DUPLICATE',
        ]);
    }
}
