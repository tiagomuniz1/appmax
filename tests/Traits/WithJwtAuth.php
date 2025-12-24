<?php

namespace Tests\Traits;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

trait WithJwtAuth
{
    protected function authHeaders(?User $user = null): array
    {
        $user = $user ?? User::factory()->create();
        $token = JWTAuth::fromUser($user);

        return [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ];
    }
}
