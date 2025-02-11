<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Wallet;
use Laravel\Sanctum\Sanctum;
use App\Models\Transaction;

class TransactionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_transaction_flow()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Wallet::factory()->create(['user_id' => $user1->id, 'balance' => 100.00, 'is_active' => 1]);
        Wallet::factory()->create(['user_id' => $user2->id, 'balance' => 50.00, 'is_active' => 1]);

        Sanctum::actingAs($user1);
        $token = $user1->createToken('test-token')->plainTextToken;

        $depositResponse = $this->postJson('/api/user/transaction/deposit', ['amount' => 50.00], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $depositResponse->assertStatus(200)
            ->assertJson(['message' => 'Deposit successful', 'ok' => true]);

        $transferResponse = $this->postJson('/api/user/transaction/transfer', [
            'amount' => 30.00,
            'wallet_id' => $user2->id
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $transferResponse->assertStatus(200)
            ->assertJson(['message' => 'Transfer successful', 'ok' => true]);

        $historyResponse = $this->getJson('/api/user/transaction/history', [
            'Authorization' => 'Bearer ' . $token
        ]);
        $historyResponse->assertStatus(200);

        $transaction = Transaction::where('from_user_id', $user1->id)
            ->where('to_user_id', $user2->id)
            ->first();

        $cancelResponse = $this->putJson("/api/user/transaction/cancel/{$transaction->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $cancelResponse->assertStatus(200)
            ->assertJson(['message' => 'Transaction cancelled', 'ok' => true]);
    }
}
