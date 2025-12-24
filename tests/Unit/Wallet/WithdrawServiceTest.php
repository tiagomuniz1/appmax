<?php

namespace Tests\Unit\Wallet;

use Tests\TestCase;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Validation\ValidationException;

class WithdrawServiceTest extends TestCase
{
    public function test_withdraws_money_from_wallet()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create([
            'balance' => 200,
            'daily_withdraw_limit' => 300,
        ]);

        $service = app(WalletService::class);
        $service->withdraw($user, 50);

        $wallet->refresh();

        $this->assertEquals(150, $wallet->balance);
    }

    public function test_does_not_allow_withdraw_with_insufficient_balance()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $user->wallet()->create([
            'balance' => 30,
            'daily_withdraw_limit' => 500,
        ]);

        $service = app(WalletService::class);
        $service->withdraw($user, 100);
    }
}
