<?php

namespace Tests\Unit\Wallet;

use Tests\TestCase;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Validation\ValidationException;

class TransferServiceTest extends TestCase
{
    public function test_transfers_money_between_users()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $senderWallet = $sender->wallet()->create(['balance' => 100]);
        $recipientWallet = $recipient->wallet()->create(['balance' => 20]);

        $service = app(WalletService::class);
        $service->transfer($sender, $recipient->email, 40);

        $senderWallet->refresh();
        $recipientWallet->refresh();

        $this->assertEquals(60, $senderWallet->balance);
        $this->assertEquals(60, $recipientWallet->balance);
    }

    public function test_does_not_allow_transfer_to_self()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $user->wallet()->create(['balance' => 100]);

        $service = app(WalletService::class);
        $service->transfer($user, $user->email, 10);
    }


    public function test_does_not_allow_transfer_with_insufficient_balance(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Saldo insuficiente.');

        // sender
        $sender = User::factory()->create();
        $senderWallet = $sender->wallet()->create([
            'balance' => 50,
        ]);

        // recipient
        $recipient = User::factory()->create();
        $recipient->wallet()->create([
            'balance' => 0,
        ]);

        $service = app(WalletService::class);

        // tenta transferir mais do que o saldo
        $service->transfer(
            sender: $sender,
            recipientEmail: $recipient->email,
            amount: 100
        );

        // garantias extras (não chega aqui, mas documenta intenção)
        $this->assertEquals(50, $senderWallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }
}
