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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('User-named loan');
            $table->float('amount');
            $table->foreignId('currency_id')->comment('Currency in which the loan was issued')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('term')->comment('Number of months for which the loan was issued');
            $table->date('first_payment')->nullable()->comment('Date on which the first payment was made');
            $table->foreignId('user_id')->comment('Borrower')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('loans');
    }
};
