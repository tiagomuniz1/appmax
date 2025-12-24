<?php

namespace Tests\Feature\Wallet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_transfer_money()
    {
        $sender = User::factory()->create(['email' => 'sender@test.com']);
        $recipient = User::factory()->create(['email' => 'recipient@test.com']);

        $sender->wallet()->create(['balance' => 200]);
        $recipient->wallet()->create(['balance' => 50]);

        $token = auth('api')->login($sender);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/v1/wallet/transfer', [
                'email' => 'recipient@test.com',
                'amount' => 100,
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('wallets', ['balance' => 100]);
        $this->assertDatabaseHas('wallets', ['balance' => 150]);
    }
}
