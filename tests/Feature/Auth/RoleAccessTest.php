<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_area(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_participant_is_forbidden_from_admin_area(): void
    {
        $participant = User::factory()->create();

        $this->actingAs($participant)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_open_the_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard Admin');
    }

    public function test_registration_page_is_available_only_to_guests(): void
    {
        $this->get('/register')->assertOk();

        $this->actingAs(User::factory()->create())
            ->get('/register')
            ->assertRedirect(route('home'));
    }
}
