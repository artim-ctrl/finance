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
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255)->comment('User-named or common type of expense');
            $table->foreignId('user_id')->comment('User who made type of expense')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'user_id',
                'name',
            ], 'user_id-name-unique-index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_types');
    }
};
