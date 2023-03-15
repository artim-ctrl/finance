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
        Schema::create('month_rows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('month_id')->comment('Month Id')->constrained('calendar_months')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 50);
            $table->float('amount');
            $table->foreignId('currency_id')->comment('Currency Id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('month_rows');
    }
};
