<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     |--------------------------------------------------------------------------
     | One row per (member, rank level) reward paid. The unique key stops a
     | level from ever being paid twice, and the row keeps the audit trail:
     | the pair counts at payout time and which transaction credited it.
     | rank_reward_baselines holds each member's pairs at launch.
     |--------------------------------------------------------------------------
     */
    public function up(): void
    {
        Schema::create('rank_reward_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('level');
            $table->string('rank_name', 100);
            $table->unsignedInteger('pairs_step');
            $table->unsignedInteger('pairs_required');
            $table->unsignedInteger('left_count');
            $table->unsignedInteger('right_count');
            $table->unsignedInteger('pairs_at_payout');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'level'], 'rank_reward_user_level_unique');
            $table->index('created_at', 'rank_reward_created_index');
        });

        // Lifetime pairs each member already had when Rank & Rewards went
        // live. Only pairs made after launch count, so these are subtracted.
        // Filled automatically the first time the rank code runs.
        Schema::create('rank_reward_baselines', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->unsignedInteger('left_count');
            $table->unsignedInteger('right_count');
            $table->unsignedInteger('pairs');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rank_reward_baselines');
        Schema::dropIfExists('rank_reward_payouts');
    }
};
