<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;
use Illuminate\Validation\ValidationException;

class AuthService
{
    
    public function register(string $name, string $email, string $password): User
    {
        if (User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already taken'],
            ]);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $user->wallet()->create([
            'balance' => 0,
        ]);

        return $user;
    }

    public function login(string $email, string $password): array
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');

        if (! $token = $guard->attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $guard->factory()->getTTL() * 60,
        ];
    }
}
