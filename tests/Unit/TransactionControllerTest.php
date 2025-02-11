<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Laravel\Sanctum\Sanctum;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_deposit()
    {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100.00, 'is_active' => 1]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/user/transaction/deposit', ['amount' => 50.00], [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Deposit successful', 'ok' => true]);
    }

    public function test_authenticated_user_can_transfer()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user1->id, 'balance' => 100.00, 'is_active' => 1]);
        Wallet::factory()->create(['user_id' => $user2->id, 'balance' => 50.00, 'is_active' => 1]);
        Sanctum::actingAs($user1);

        $response = $this->postJson('/api/user/transaction/transfer', ['amount' => 30.00, 'wallet_id' => $user2->id], [
            'Authorization' => 'Bearer ' . $user1->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Transfer successful', 'ok' => true]);
    }

    public function test_authenticated_user_can_get_transaction_history()
    {
        $user = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100.00, 'is_active' => 1]);
        Transaction::factory()->create(['from_user_id' => $user->id, 'to_user_id' => $user->id, 'amount' => 50.00, 'type' => 'deposit']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/transaction/history', [
            'Authorization' => 'Bearer ' . $user->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_cancel_transaction()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Wallet::factory()->create(['user_id' => $user1->id, 'balance' => 100.00, 'is_active' => 1]);
        Wallet::factory()->create(['user_id' => $user2->id, 'balance' => 50.00, 'is_active' => 1]);
        $transaction = Transaction::factory()->create(['from_user_id' => $user1->id, 'to_user_id' => $user2->id, 'amount' => 30.00, 'type' => 'transfer']);
        Sanctum::actingAs($user1);

        $response = $this->putJson('/api/user/transaction/cancel/' . $transaction->id, [], [
            'Authorization' => 'Bearer ' . $user1->createToken('test-token')->plainTextToken
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Transaction cancelled', 'ok' => true]);
    }
}
