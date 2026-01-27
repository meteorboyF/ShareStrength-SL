<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if conversations table already exists
        if (Schema::hasTable('conversations')) {
            // Just ensure indexes exist if needed, but skip table creation
            return;
        }

        // Create conversations table using Laravel Schema builder (database-agnostic)
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_one_id');
            $table->string('user_one_type');
            $table->unsignedBigInteger('user_two_id');
            $table->string('user_two_type');
            $table->unsignedBigInteger('task_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['user_one_id', 'user_one_type'], 'idx_user_one');
            $table->index(['user_two_id', 'user_two_type'], 'idx_user_two');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
