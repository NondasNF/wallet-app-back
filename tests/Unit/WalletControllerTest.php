<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Wallet;
use Laravel\Sanctum\Sanctum;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_wallet()
    {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/wallet', [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['ok']);
    }

    public function test_authenticated_user_can_change_wallet_status()
    {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id, 'is_active' => 0]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/wallet', ['status' => 0], [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Wallet status updated', 'ok' => true]);
    }

    public function test_wallet_status_update_requires_valid_status()
    {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user/wallet', ['status' => 2], [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['errors']);
    }
}
