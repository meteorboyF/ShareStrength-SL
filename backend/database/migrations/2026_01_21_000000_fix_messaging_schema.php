<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_one_id');
                $table->string('user_one_type')->default('user');
                $table->unsignedBigInteger('user_two_id');
                $table->string('user_two_type')->default('user');
                $table->unsignedBigInteger('task_id')->nullable();
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();

                $table->index(['user_one_id', 'user_one_type'], 'idx_user_one');
                $table->index(['user_two_id', 'user_two_type'], 'idx_user_two');
            });
        } else {
            Schema::table('conversations', function (Blueprint $table) {
                if (!Schema::hasColumn('conversations', 'user_one_type')) {
                    $table->string('user_one_type')->default('user');
                }
                if (!Schema::hasColumn('conversations', 'user_two_type')) {
                    $table->string('user_two_type')->default('user');
                }
                if (!Schema::hasColumn('conversations', 'task_id')) {
                    $table->unsignedBigInteger('task_id')->nullable();
                }
                if (!Schema::hasColumn('conversations', 'last_message_at')) {
                    $table->timestamp('last_message_at')->nullable();
                }
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('conversation_id')->nullable();
                $table->unsignedBigInteger('task_id')->nullable();
                $table->unsignedBigInteger('sender_id');
                $table->string('sender_type')->default('user');
                $table->unsignedBigInteger('receiver_id');
                $table->string('receiver_type')->default('user');
                $table->text('content');
                $table->boolean('is_read')->default(false);
                $table->timestamps();

                $table->index('conversation_id');
                $table->index('sender_id');
                $table->index('receiver_id');
            });
        } else {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'conversation_id')) {
                    $table->unsignedBigInteger('conversation_id')->nullable()->index();
                }
                if (!Schema::hasColumn('messages', 'sender_type')) {
                    $table->string('sender_type')->default('user');
                }
                if (!Schema::hasColumn('messages', 'receiver_type')) {
                    $table->string('receiver_type')->default('user');
                }
                if (!Schema::hasColumn('messages', 'is_read')) {
                    $table->boolean('is_read')->default(false);
                }
                if (!Schema::hasColumn('messages', 'task_id')) {
                    $table->unsignedBigInteger('task_id')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'conversation_id')) {
                    $table->dropColumn('conversation_id');
                }
                if (Schema::hasColumn('messages', 'sender_type')) {
                    $table->dropColumn('sender_type');
                }
                if (Schema::hasColumn('messages', 'receiver_type')) {
                    $table->dropColumn('receiver_type');
                }
                if (Schema::hasColumn('messages', 'is_read')) {
                    $table->dropColumn('is_read');
                }
                if (Schema::hasColumn('messages', 'task_id')) {
                    $table->dropColumn('task_id');
                }
            });
        }

        if (Schema::hasTable('conversations')) {
            Schema::dropIfExists('conversations');
        }
    }
};
