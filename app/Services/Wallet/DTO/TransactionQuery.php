<?php

namespace App\Services\Wallet\DTO;

class TransactionQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly string $order
    ) {}
}
