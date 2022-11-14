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

            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('currency_id_from')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount_from');
            $table->foreignId('currency_id_to')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount_to');
            $table->dateTime('exchanged_at');

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
