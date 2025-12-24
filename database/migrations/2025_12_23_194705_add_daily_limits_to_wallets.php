<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->decimal('daily_withdraw_limit', 15, 2)->default(100);
            $table->decimal('daily_deposit_limit', 15, 2)->default(500);
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'daily_withdraw_limit',
                'daily_deposit_limit'
            ]);
        });
    }
};
