<?php

namespace Tests\Unit\Wallet;

use Tests\TestCase;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Validation\ValidationException;

class DepositServiceTest extends TestCase
{
    public function test_deposits_money_into_wallet()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create([
            'balance' => 100,
            'daily_deposit_limit' => 1000,
        ]);

        $service = app(WalletService::class);

        $service->deposit($user, 50);

        $wallet->refresh();

        $this->assertEquals(150, $wallet->balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 50,
        ]);
    }

    public function test_does_not_allow_deposit_above_daily_limit()
    {
        $this->expectException(ValidationException::class);

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
            'created_at' => now(),
        ]);

        $service = app(WalletService::class);

        $service->deposit($user, 30);
    }
}
