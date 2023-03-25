<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->unique([
                'user_id',
                'currency_id',
            ], 'user_id-currency_id-unique-index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['currency_id']);
            $table->dropUnique('user_id-currency_id-unique-index');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
