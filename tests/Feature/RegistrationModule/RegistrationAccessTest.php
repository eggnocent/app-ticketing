<?php

namespace Tests\Feature\RegistrationModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event;
use App\Modules\RegistrationModule\Models\IssuedTicket;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Models\RegistrationTicket;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_registration_routes(): void
    {
        $this->get(route('registrations.index'))->assertRedirect(route('login'));
        $this->get(route('checkout.create', 'any-event'))->assertRedirect(route('login'));
    }

    public function test_participant_sees_only_their_own_registrations(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Registration::factory()->create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-MINE',
        ]);

        Registration::factory()->create([
            'user_id' => $other->id,
            'invoice_number' => 'INV-OTHER',
        ]);

        $response = $this->actingAs($user)->get(route('registrations.index'));

        $response->assertOk()->assertSee('INV-MINE')->assertDontSee('INV-OTHER');
    }

    public function test_participant_cannot_open_another_participants_registration(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $registration = Registration::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get(route('registrations.show', $registration))
            ->assertForbidden();
    }

    public function test_participant_can_open_their_own_e_ticket(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->published()->create();
        $ticket = Ticket::factory()->forEvent($event->id)->create();

        $registration = Registration::factory()->create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-ETICKET',
        ]);

        $line = RegistrationTicket::create([
            'registration_id' => $registration->id,
            'ticket_id' => $ticket->id,
            'quantity' => 1,
            'unit_price_rupiah' => $ticket->price_rupiah,
            'subtotal_rupiah' => $ticket->price_rupiah,
        ]);

        IssuedTicket::create([
            'registration_ticket_id' => $line->id,
            'ticket_code' => 'TKT-ETICKET-CODE',
        ]);

        $this->actingAs($user)
            ->get(route('registrations.show', $registration))
            ->assertOk()
            ->assertSee('INV-ETICKET')
            ->assertSee('TKT-ETICKET-CODE');
    }

    public function test_participant_is_forbidden_from_admin_registration_recap(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.registrations.index'))
            ->assertForbidden();
    }

    public function test_admin_can_open_registration_recap(): void
    {
        $user = User::factory()->create();

        Registration::factory()->create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-REKAP',
            'total_rupiah' => 150000,
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.registrations.index'))
            ->assertOk()
            ->assertSee('INV-REKAP')
            ->assertSee($user->name);
    }
}
