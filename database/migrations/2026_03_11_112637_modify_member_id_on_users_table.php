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
    Schema::table('users', function (Blueprint $table) {
        // Only add if it doesn't exist
        if (!Schema::hasColumn('users', 'member_id')) {
            $table->string('member_id')->nullable()->after('id');
        }
    });

    // Fill existing users so they have a valid ID
    $users = \DB::table('users')->get();
    foreach ($users as $user) {
        $newId = 'HGNL' . str_pad($user->id + 10000, 8, '0', STR_PAD_LEFT);
        \DB::table('users')->where('id', $user->id)->update(['member_id' => $newId]);
    }

    Schema::table('users', function (Blueprint $table) {
        // Now make it unique and required
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
