<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     |--------------------------------------------------------------------------
     | Superadmin kill switches: one row per switch (see App\Services\SystemSwitch)
     | and a log of every time one was turned on or off, and by whom.
     |--------------------------------------------------------------------------
     */
    public function up(): void
    {
        Schema::create('system_switches', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->boolean('is_on')->default(false);
            $table->string('message', 500)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('system_switch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50);
            $table->boolean('is_on');
            $table->string('message', 500)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('created_at', 'system_switch_logs_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_switch_logs');
        Schema::dropIfExists('system_switches');
    }
};
