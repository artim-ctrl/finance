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
        Schema::table('incomes', function (Blueprint $table) {
            $table->integer('increase_month')->nullable()->after('user_id');
            $table->float('increase_amount')->nullable()->after('increase_month');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('increase_month');
            $table->dropColumn('increase_amount');
        });
    }
};
