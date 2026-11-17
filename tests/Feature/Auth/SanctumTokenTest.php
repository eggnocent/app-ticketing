<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_route_accepts_a_sanctum_bearer_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Peserta Token',
            'email' => 'token@example.test',
        ]);

        $token = $user->createToken('feature-test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', 'token@example.test');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'feature-test',
        ]);
    }

    public function test_api_route_rejects_requests_without_a_token(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }
}
