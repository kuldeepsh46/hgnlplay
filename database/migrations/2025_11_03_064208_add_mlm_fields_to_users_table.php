<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->unique()->after('id');
            $t->unsignedBigInteger('sponsor_id')->nullable()->index(); // who referred
            $t->string('leg')->nullable(); // 'L' or 'R' relative to sponsor (marketing)
            $t->decimal('wallet_balance', 12, 2)->default(0);
            // Binary volumes for pairing
            $t->unsignedBigInteger('left_volume')->default(0);
            $t->unsignedBigInteger('right_volume')->default(0);
            $t->unsignedBigInteger('carry_left')->default(0);
            $t->unsignedBigInteger('carry_right')->default(0);
            $t->foreign('sponsor_id')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('sponsor_id');
            $t->dropColumn([
            'username','sponsor_id','leg','wallet_balance',
            'left_volume','right_volume','carry_left','carry_right'
            ]);
        });
    }

};
