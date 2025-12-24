<?php

namespace Tests\Feature\Wallet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_deposit_money()
    {
        $user = User::factory()->create();
        $user->wallet()->create([
            'balance' => 0,
            'daily_deposit_limit' => 500,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/wallet/deposit', [
                'amount' => 100,
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('wallets', [
            'balance' => 100,
        ]);
    }

    public function test_user_cannot_deposit_above_daily_limit()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create([
            'balance' => 0,
            'daily_deposit_limit' => 100,
        ]);

        $wallet->transactions()->create([
            'type' => 'deposit',
            'amount' => 80,
            'balance_before' => 0,
            'balance_after' => 80,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/wallet/deposit', [
                'amount' => 30,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['amount']);
    }
}
