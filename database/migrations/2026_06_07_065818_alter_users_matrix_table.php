<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_matrix_progress', function (Blueprint $table) {
            if (Schema::hasColumn('user_matrix_progress', 'rank_level')) {
                $table->dropColumn('rank_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_matrix_progress', function (Blueprint $table) {
            $table->integer('rank_level')->default(1);
        });
    }
};
