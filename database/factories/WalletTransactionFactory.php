<?php

namespace Database\Factories;

use App\Models\WalletTransaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    public function definition()
    {
        return [
            'wallet_id' => Wallet::factory(),
            'type' => $this->faker->randomElement(['deposit', 'withdraw', 'transfer_in', 'transfer_out']),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'balance_before' => $this->faker->randomFloat(2, 0, 1000),
            'balance_after' => $this->faker->randomFloat(2, 100, 2000),
        ];
    }
}
