<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    // Use Schema::table to ALTER the existing table
    if (Schema::hasTable('users')) {
        Schema::table('users', function (Blueprint $table) {
            // Only add if they don't exist to avoid "Duplicate column" errors
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'is_superadmin')) {
                $table->boolean('is_superadmin')->default(false)->after('is_admin');
            }
        });
    }
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['is_admin', 'is_superadmin']);
    });
}
};
