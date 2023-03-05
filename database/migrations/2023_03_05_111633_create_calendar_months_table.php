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
        Schema::create('calendar_months', function (Blueprint $table) {
            $table->id();

            $table->foreignId('calendar_id')->comment('Calendar Id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->smallInteger(column: 'year', unsigned: true);
            $table->tinyInteger(column: 'month', unsigned: true);

            $table->timestamps();

            $table->unique([
                'calendar_id',
                'year',
                'month',
            ], 'calendar_id-year-month-unique-index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_months');
    }
};
