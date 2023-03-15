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
        Schema::create('goal_steps', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255)->comment('User-named step of the goal');
            $table->foreignId('goal_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('estimated_currency_id')->comment('Estimated value currency')->constrained('currencies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('estimated_amount')->comment('Estimated amount');

            $table->foreignId('currency_id')->comment('Actual value currency')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount')->comment('Actual amount')->nullable();

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
        Schema::dropIfExists('goal_steps');
    }
};
