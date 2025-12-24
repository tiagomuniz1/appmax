<?php

namespace Tests\Feature\Wallet;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_see_balance()
    {
        $user = User::factory()->create();
        $user->wallet()->create([
            'balance' => 150.50,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/v1/wallet/balance');

        $response->assertOk()
            ->assertJson([
                'balance' => 150.50,
            ]);
    }
}
