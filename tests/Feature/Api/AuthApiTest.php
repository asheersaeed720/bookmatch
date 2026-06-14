<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_register_and_receive_a_token(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Mobile User',
            'email'                 => 'mobile@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'role'], 'token'])
            ->assertJsonPath('data.email', 'mobile@example.com')
            ->assertJsonPath('data.role', 'student');

        $user = User::where('email', 'mobile@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('student'));
    }

    public function test_a_user_can_login_and_receive_a_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'email'], 'token'])
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(422);
    }

    public function test_protected_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/dashboard')->assertUnauthorized();
        $this->getJson('/api/v1/profile')->assertUnauthorized();
    }

    public function test_a_user_can_logout_and_revoke_the_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_authenticated_user_can_fetch_themselves(): void
    {
        $user = User::factory()->create();

        $this->actingAsApi($user)
            ->getJson('/api/v1/auth/user')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }
}
