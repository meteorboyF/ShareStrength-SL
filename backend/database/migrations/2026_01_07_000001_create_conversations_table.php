<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('conversations')) {
            return; // Table already exists, skip
        }

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            // User One
            $table->unsignedBigInteger('user_one_id');
            $table->string('user_one_type')->default('user'); // 'user' or 'helper'
            
            // User Two
            $table->unsignedBigInteger('user_two_id');
            $table->string('user_two_type')->default('user'); // 'user' or 'helper'

            // Task FK
            $table->unsignedBigInteger('task_id')->nullable();
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Ensure unique conversations between two users for the same task context
            $table->unique(['user_one_id', 'user_one_type', 'user_two_id', 'user_two_type', 'task_id'], 'unique_conversation');
        });

        // Create messages table
        if (Schema::hasTable('messages')) {
            return; // Table already exists, skip
        }

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->unsignedBigInteger('task_id')->nullable();
            
            // Sender
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type')->default('user');
            
            // Receiver
            $table->unsignedBigInteger('receiver_id');
            $table->string('receiver_type')->default('user');
            
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });

        Schema::dropIfExists('conversations');
    }
};
