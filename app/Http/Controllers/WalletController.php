<?php

namespace App\Http\Controllers;

use App\Http\Requests\Wallet\DepositRequest;
use App\Http\Requests\Wallet\TransactionListRequest;
use App\Http\Requests\Wallet\TransferRequest;
use App\Http\Requests\Wallet\WithdrawRequest;
use App\Http\Resources\Wallet\WalletTransactionResource;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    
    public function balance(Request $request, WalletService $walletService)
    {
        $balance = $walletService->balance($request->user());

        return response()->json([
            'balance' => $balance,
        ]);
    }
    
    public function deposit(
        DepositRequest $request,
        WalletService $walletService
    ) {
        $walletService->deposit(
            user: $request->user(),
            amount: $request->amount
        );

        return response()->noContent();
    }
    
    public function withdraw(
        WithdrawRequest $request,
        WalletService $walletService
    ) {
        $walletService->withdraw(
            user: $request->user(),
            amount: $request->amount
        );

        return response()->noContent();
    }

    public function transfer(
        TransferRequest $request,
        WalletService $walletService
    ) {
        $walletService->transfer(
            sender: $request->user(),
            recipientEmail: $request->email,
            amount: $request->amount
        );

        return response()->noContent();
    }

    public function transactions(
        TransactionListRequest $request,
        WalletService $walletService
    ) {
        $paginator = $walletService->transactions(
            user: $request->user(),
            query: $request->toQuery()
        );
        
        return response()->json([
            'data' => WalletTransactionResource::collection($paginator->items()),
            'meta' => [
                'page'      => $paginator->currentPage(),
                'per_page'  => $paginator->perPage(),
                'total'     => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

}
