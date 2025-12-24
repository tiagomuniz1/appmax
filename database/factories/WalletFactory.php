<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class WalletFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'balance' => 0,
            'daily_deposit_limit' => 100,
        ];
    }
}
