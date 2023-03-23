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
        Schema::create('balance_history', function (Blueprint $table) {
            $table->id();

            $table->enum('action', ['minus', 'plus'])->comment('Allowed actions');
            $table->foreignId('balance_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('amount_from')->comment('How much money was');
            $table->float('amount_to')->comment('How much money became');
            $table->enum('entity_type', ['expenses', 'exchanges', 'loans', 'incomes'])->comment('What caused the balance change');
            $table->unsignedBigInteger('entity_id')->comment('Entity id');
            $table->dateTime('done_at')->comment('Done at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_history');
    }
};
