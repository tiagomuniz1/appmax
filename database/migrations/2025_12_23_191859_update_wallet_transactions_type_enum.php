<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE wallet_transactions
            MODIFY type ENUM(
                'deposit',
                'withdraw',
                'transfer_out',
                'transfer_in'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE wallet_transactions
            MODIFY type ENUM(
                'deposit',
                'withdraw'
            ) NOT NULL
        ");
    }
};
