<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('bonus_type')->nullable()->after('type')->index();
        });

        // Optional backfill for old records
        DB::table('transactions')
            ->where('remarks', 'like', 'Pair Completion Bonus 2000 Package:%')
            ->update(['bonus_type' => 'pair_bonus_2000']);

        DB::table('transactions')
            ->where('remarks', 'like', 'Pair Completion Bonus Normal Package:%')
            ->update(['bonus_type' => 'pair_bonus_normal']);

        DB::table('transactions')
            ->whereNull('bonus_type')
            ->where('remarks', 'like', 'Pair Completion Bonus:%')
            ->update(['bonus_type' => 'pair_bonus_normal']);

        DB::table('transactions')
            ->whereNull('bonus_type')
            ->where('remarks', 'like', 'Level Income%')
            ->update(['bonus_type' => 'level_income']);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('bonus_type');
        });
    }
};