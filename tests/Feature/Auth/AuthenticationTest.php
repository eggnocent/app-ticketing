<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_participant_and_logs_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Peserta Baru',
            'email' => 'peserta.baru@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'peserta.baru@example.test',
            'role' => 'peserta',
        ]);
    }

    public function test_register_ignores_role_sent_by_the_client(): void
    {
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'penyusup@example.test',
            'role' => 'peserta',
        ]);
    }

    public function test_login_authenticates_a_valid_user(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta1@example.test',
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => 'peserta1@example.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_without_leaking_whether_the_email_exists(): void
    {
        $response = $this->post('/login', [
            'email' => 'tidak.ada@example.test',
            'password' => 'password',
        ]);

        $response->assertInvalid(['email' => trans('auth.failed')]);
        $this->assertGuest();
    }

    public function test_logout_invalidates_the_session(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
