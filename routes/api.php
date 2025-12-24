<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    /*
    |--------------------------------------------------------------------------
    | Protected routes (JWT)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {

        // Usuário autenticado
        Route::get('/me', function (Illuminate\Http\Request $request) {
            return $request->user();
        });

        // Wallet
        Route::prefix('wallet')->group(function () {
            Route::get('/balance', [WalletController::class, 'balance']);
            Route::post('/deposit', [WalletController::class, 'deposit']);
            Route::post('/withdraw', [WalletController::class, 'withdraw']);
            Route::post('/transfer', [WalletController::class, 'transfer']);
            Route::get('/transactions', [WalletController::class, 'transactions']);
        });
    });
});
