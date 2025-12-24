<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function register(
        RegisterRequest $request,
        AuthService $authService
    ) {
        $user = $authService->register(
            name: $request->name,
            email: $request->email,
            password: $request->password
        );

        return response()->json([
            'user' => $user,
        ], 201);
    }

    public function login(
        LoginRequest $request,
        AuthService $authService
    ) {
        $tokenData = $authService->login(
            email: $request->email,
            password: $request->password
        );

        return response()->json($tokenData);
    }
}
