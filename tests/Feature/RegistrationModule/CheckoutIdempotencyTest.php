<?php

namespace Tests\Feature\RegistrationModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event as EventModel;
use App\Modules\RegistrationModule\Models\IssuedTicket;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeated_submission_with_the_same_key_returns_the_same_registration(): void
    {
        $user = User::factory()->create();
        $ticket = $this->availableTicket();
        $key = (string) Str::uuid();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 2],
                'idempotency_key' => $key,
            ])
            ->assertRedirect();

        $registration = Registration::query()->sole();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 2],
                'idempotency_key' => $key,
            ])
            ->assertRedirect(route('registrations.show', $registration));

        $this->assertSame(1, Registration::query()->count());
        $this->assertSame(3, $ticket->refresh()->remaining_quota);
        $this->assertSame(2, IssuedTicket::query()->count());
        $this->assertSame($key, $registration->refresh()->idempotency_key);
    }

    public function test_different_keys_create_separate_registrations(): void
    {
        $user = User::factory()->create();
        $ticket = $this->availableTicket();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
                'idempotency_key' => (string) Str::uuid(),
            ])
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
                'idempotency_key' => (string) Str::uuid(),
            ])
            ->assertRedirect();

        $this->assertSame(2, Registration::query()->count());
        $this->assertSame(3, $ticket->refresh()->remaining_quota);
    }

    public function test_the_same_key_is_scoped_to_each_user(): void
    {
        $key = (string) Str::uuid();
        $ticket = $this->availableTicket();

        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
                'idempotency_key' => $key,
            ])
            ->assertRedirect();

        $this->actingAs($secondUser)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
                'idempotency_key' => $key,
            ])
            ->assertRedirect();

        $this->assertSame(2, Registration::query()->count());
        $this->assertSame(3, $ticket->refresh()->remaining_quota);
    }

    public function test_idempotency_key_must_be_a_uuid(): void
    {
        $user = User::factory()->create();
        $ticket = $this->availableTicket();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'quantities' => [$ticket->id => 1],
                'idempotency_key' => 'not-a-uuid',
            ])
            ->assertSessionHasErrors('idempotency_key');

        $this->assertSame(0, Registration::query()->count());
    }

    private function availableTicket(): Ticket
    {
        $event = EventModel::factory()->published()->create();

        return Ticket::factory()->forEvent($event->id)->create([
            'initial_quota' => 5,
            'remaining_quota' => 5,
        ]);
    }
}
