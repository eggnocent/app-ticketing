<?php

namespace Tests\Feature\RegistrationModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event as EventModel;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_open_the_checkout_page(): void
    {
        $admin = User::factory()->admin()->create();
        $event = EventModel::factory()->published()->create();

        $this->actingAs($admin)
            ->get(route('checkout.create', $event->slug))
            ->assertForbidden();
    }

    public function test_admin_cannot_submit_a_checkout(): void
    {
        $admin = User::factory()->admin()->create();
        $event = EventModel::factory()->published()->create();
        $ticket = Ticket::factory()->forEvent($event->id)->create([
            'initial_quota' => 5,
            'remaining_quota' => 5,
        ]);

        $this->actingAs($admin)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
            ])
            ->assertForbidden();

        $this->assertSame(0, Registration::query()->count());
        $this->assertSame(5, $ticket->refresh()->remaining_quota);
    }

    public function test_admin_cannot_open_participant_registration_history(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('registrations.index'))
            ->assertForbidden();
    }

    public function test_public_event_page_hides_checkout_from_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $event = EventModel::factory()->published()->create();

        $this->actingAs($admin)
            ->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertDontSee('Beli Tiket')
            ->assertDontSee('Pesanan Saya')
            ->assertSee('Akun admin tidak dapat membeli tiket.');
    }

    public function test_public_event_page_shows_checkout_to_participant(): void
    {
        $event = EventModel::factory()->published()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertSee('Beli Tiket')
            ->assertSee('Pesanan Saya');
    }
}
