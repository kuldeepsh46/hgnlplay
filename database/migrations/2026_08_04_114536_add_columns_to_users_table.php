<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds two running-total counters used by TopupController's binary
     * pair-income logic to permanently "flush" matched volume once it has
     * been evaluated for the day — this is what stops the 5,000/day cap
     * from re-paying the same old matched volume on a later day.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('normal_pair_processed_volume', 15, 2)
                ->default(0)
                ->after('investment_count')
                ->comment('Cumulative matched business volume already processed for normal (10%) pair income.');

            $table->decimal('starter_pair_processed_volume', 15, 2)
                ->default(0)
                ->after('normal_pair_processed_volume')
                ->comment('Cumulative Starter Package (₹1600) matched volume already processed for the flat ₹300 pair bonus.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['normal_pair_processed_volume', 'starter_pair_processed_volume']);
        });
    }
};