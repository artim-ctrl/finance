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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('User-named income');
            $table->smallInteger('day_receiving')->comment('Monthly income date');
            $table->foreignId('currency_id')->comment('The currency in which the user receives income')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount');
            $table->foreignId('user_id')->comment('Income recipient')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('incomes');
    }
};
