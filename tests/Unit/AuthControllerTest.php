<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token', 'user', 'message', 'ok']);
        
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'differentpassword'
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([ 'password' => Hash::make('password123') ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'user', 'message', 'ok']);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_authenticated_user_can_get_their_info()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user', [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['user' => ['id' => $user->id], 'ok' => true]);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/user/logout', [], [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logged out', 'ok' => true]);
    }

    public function test_authenticated_user_can_logout_from_all_devices()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/user/logout-all', [], [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logged out from all devices', 'ok' => true]);
    }

    public function test_authenticated_user_can_get_logged_devices()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/logged-devices', [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['ok' => true]);
    }
}
