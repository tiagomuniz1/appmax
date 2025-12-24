<?php

namespace Tests\Unit\Wallet;

use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Models\User;
use App\Services\Wallet\WalletService;

class WalletServiceTest extends TestCase
{
    public function test_returns_user_wallet_balance()
    {
        $user = User::factory()->create();
        $user->wallet()->create(['balance' => 150.75]);

        $service = app(WalletService::class);

        $balance = $service->balance($user);

        $this->assertEquals(150.75, $balance);
    }

    public function test_throws_exception_if_wallet_does_not_exist()
    {
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Wallet not found');

        $user = User::factory()->create();

        $service = app(WalletService::class);
        $service->balance($user);
    }
}
