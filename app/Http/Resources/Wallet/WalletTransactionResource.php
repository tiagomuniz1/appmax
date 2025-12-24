<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'type'            => $this->type,
            'amount'          => (float) $this->amount,
            'balance_before' => (float) $this->balance_before,
            'balance_after'  => (float) $this->balance_after,
            'created_at'     => $this->created_at->toISOString(),
        ];
    }
}
