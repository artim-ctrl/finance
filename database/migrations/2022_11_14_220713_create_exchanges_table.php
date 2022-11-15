<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->comment('User who made the exchange')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('balance_id_from')->comment('The balance from which the exchange was made')->constrained('balances')->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount_from')->comment('Exchange amount from');
            $table->foreignId('balance_id_to')->comment('The balance to which the exchange was made')->constrained('balances')->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount_to')->comment('Exchange amount to');
            $table->dateTime('exchanged_at')->comment('Date of exchange');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
