<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Services\Wallet\DTO\TransactionQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{

    private function walletOrFail(User $user)
    {
        return $user->wallet
            ?? throw ValidationException::withMessages([
                'wallet' => ['Wallet not found'],
            ]);
    }

    private function validateDeposit($wallet, float $amount): void
    {
        $todayTotal = $wallet->transactions()
            ->where('type', 'deposit')
            ->whereDate('created_at', now())
            ->sum('amount');

        if (($todayTotal + $amount) > $wallet->daily_deposit_limit) {
            throw ValidationException::withMessages([
                'amount' => ['Limite diário de depósito excedido'],
            ]);
        }
    }

    private function validateWithdraw($wallet, float $amount): void
    {
        if ($wallet->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => ['Saldo insuficiente'],
            ]);
        }

        $todayTotal = $wallet->transactions()
            ->where('type', 'withdraw')
            ->whereDate('created_at', now())
            ->sum('amount');

        if (($todayTotal + $amount) > $wallet->daily_withdraw_limit) {
            throw ValidationException::withMessages([
                'amount' => ['Limite diário de saque excedido'],
            ]);
        }
    }


    public function balance(User $user): float
    {
        return $this->walletOrFail($user)->balance;
    }

    public function deposit(User $user, float $amount): void
    {
        $wallet = $this->walletOrFail($user);

        $this->validateDeposit($wallet, $amount);

        DB::transaction(function () use ($wallet, $amount) {
            $before = $wallet->balance;
            $after  = round($before + $amount, 2);

            $wallet->update(['balance' => $after]);

            $wallet->transactions()->create([
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after,
            ]);
        });
    }

    public function withdraw(User $user, float $amount): void
    {
        $wallet = $this->walletOrFail($user);

        $this->validateWithdraw($wallet, $amount);

        DB::transaction(function () use ($wallet, $amount) {
            $before = $wallet->balance;
            $after  = round($before - $amount, 2);

            $wallet->update(['balance' => $after]);

            $wallet->transactions()->create([
                'type'           => 'withdraw',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $after,
            ]);
        });
    }

    public function transfer(User $sender, string $recipientEmail, float $amount): void
    {
        if ($sender->email === $recipientEmail) {
            throw ValidationException::withMessages([
                'email' => ['Não é possível transferir para o próprio usuário.'],
            ]);
        }

        $recipient = User::where('email', $recipientEmail)->first();

        $senderWallet    = $this->walletOrFail($sender);
        $recipientWallet = $this->walletOrFail($recipient);

        if ($senderWallet->balance < $amount) {
            throw ValidationException::withMessages([
                'amount' => ['Saldo insuficiente.'],
            ]);
        }

        DB::transaction(function () use (
            $senderWallet,
            $recipientWallet,
            $amount
        ) {
            // sender
            $senderBefore = $senderWallet->balance;
            $senderAfter  = round($senderBefore - $amount, 2);

            $senderWallet->update([
                'balance' => $senderAfter,
            ]);

            $senderWallet->transactions()->create([
                'type'           => 'transfer_out',
                'amount'         => $amount,
                'balance_before' => $senderBefore,
                'balance_after'  => $senderAfter,
            ]);

            // recipient
            $recipientBefore = $recipientWallet->balance;
            $recipientAfter  = round($recipientBefore + $amount, 2);

            $recipientWallet->update([
                'balance' => $recipientAfter,
            ]);

            $recipientWallet->transactions()->create([
                'type'           => 'transfer_in',
                'amount'         => $amount,
                'balance_before' => $recipientBefore,
                'balance_after'  => $recipientAfter,
            ]);
        });
    }

    public function transactions(
        User $user,
        TransactionQuery $query
    ): LengthAwarePaginator {
        $wallet = $this->walletOrFail($user);

        return $wallet->transactions()
            ->orderBy('created_at', $query->order)
            ->paginate(
                perPage: $query->perPage,
                page: $query->page
            );
    }
}
