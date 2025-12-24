<?php

namespace Tests\Feature\Wallet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_transactions()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create(['balance' => 100]);

        $wallet->transactions()->create([
            'type' => 'deposit',
            'amount' => 100,
            'balance_before' => 0,
            'balance_after' => 100,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/wallet/transactions');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['page', 'per_page', 'total', 'last_page'],
            ]);
    }
}
