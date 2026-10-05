<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     |--------------------------------------------------------------------------
     | One row per (downline user, day) the sponsor binary bonus was paid for.
     | The unique key is what stops a day from ever being paid twice, and the
     | row keeps the audit trail: what the downline earned, what was capped,
     | and which transaction credited the sponsor.
     |--------------------------------------------------------------------------
     */
    public function up(): void
    {
        Schema::create('sponsor_binary_bonus_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sponsor_id');
            $table->unsignedBigInteger('source_user_id');
            $table->date('income_date');
            $table->decimal('binary_income', 15, 2);
            $table->decimal('capped_income', 15, 2);
            $table->decimal('bonus_amount', 15, 2);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamps();

            $table->unique(['source_user_id', 'income_date'], 'sbb_payouts_source_date_unique');
            $table->index(['sponsor_id', 'income_date'], 'sbb_payouts_sponsor_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_binary_bonus_payouts');
    }
};
