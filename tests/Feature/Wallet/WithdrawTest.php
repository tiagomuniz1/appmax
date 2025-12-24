<?php

namespace Tests\Feature\Wallet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class WithdrawTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_withdraw_money()
    {
        $user = User::factory()->create();
        $user->wallet()->create([
            'balance' => 200,
            'daily_withdraw_limit' => 300,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/wallet/withdraw', [
                'amount' => 50,
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('wallets', [
            'balance' => 150,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'type' => 'withdraw',
            'amount' => 50,
        ]);
    }

    public function test_user_cannot_withdraw_with_insufficient_balance()
    {
        $user = User::factory()->create();
        $user->wallet()->create([
            'balance' => 30,
            'daily_withdraw_limit' => 100,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/wallet/withdraw', [
                'amount' => 50,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_user_cannot_withdraw_above_daily_limit()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create([
            'balance' => 500,
            'daily_withdraw_limit' => 100,
        ]);

        // saque já feito hoje
        $wallet->transactions()->create([
            'type' => 'withdraw',
            'amount' => 80,
            'balance_before' => 500,
            'balance_after' => 420,
            'created_at' => now(),
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/wallet/withdraw', [
                'amount' => 30,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }
}
