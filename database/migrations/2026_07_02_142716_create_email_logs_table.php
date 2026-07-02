<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('email');
            $table->string('event_type')->nullable();

            $table->string('subject');
            $table->string('status')->default('pending'); 
            // pending, sent, failed

            $table->text('error_message')->nullable();

            $table->json('payload')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('email');
            $table->index('event_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};