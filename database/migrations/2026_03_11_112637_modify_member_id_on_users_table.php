<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Step 1: Drop the old column
    if (Schema::hasColumn('users', 'member_id')) {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('member_id');
        });
    }

    // Step 2: Add the new column as NULLABLE first (to avoid the Duplicate error)
    Schema::table('users', function (Blueprint $table) {
        $table->string('member_id')->nullable()->after('id');
    });

    // Step 3: Fill existing users with IDs
    $users = \DB::table('users')->get();
    foreach ($users as $user) {
        $newId = 'HGNL' . str_pad($user->id + 10000, 8, '0', STR_PAD_LEFT);
        \DB::table('users')->where('id', $user->id)->update(['member_id' => $newId]);
    }

    // Step 4: Now that everyone has a unique ID, set it to NOT NULL and UNIQUE
    Schema::table('users', function (Blueprint $table) {
        $table->string('member_id')->nullable(false)->unique()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
