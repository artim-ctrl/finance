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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255)->comment('User-named expense');
            $table->foreignId('user_id')->comment('User who made the expense')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('expense_type_id')->comment('Type of expense')->constrained()->cascadeOnUpdate()->cascadeOnDelete(); // TODO: restrict?
            $table->foreignId('balance_id')->comment('The balance from which the expense was made')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('expenses');
    }
};
