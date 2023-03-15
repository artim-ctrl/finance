<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->comment('Balance owner')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('currency_id')->comment('Balance currency')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount');

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
        Schema::dropIfExists('balances');
    }
};
