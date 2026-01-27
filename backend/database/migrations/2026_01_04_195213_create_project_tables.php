<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('trusted_contacts')) {
            Schema::create('trusted_contacts', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->integer('trusted_user_id')->nullable();
                $table->foreign('trusted_user_id')->references('user_id')->on('users')->onDelete('set null');
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->enum('status', ['pending', 'verified'])->default('pending');
                $table->string('verification_token')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->integer('created_by');
                $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
                $table->integer('caregiver_id')->nullable();
                $table->foreign('caregiver_id')->references('user_id')->on('users')->onDelete('set null');
                $table->string('title');
                $table->text('description');
                $table->string('location')->nullable();
                $table->decimal('budget', 10, 2)->nullable();
                $table->enum('status', ['open', 'requested', 'accepted', 'completed', 'cancelled'])->default('open');
                $table->json('required_skills')->nullable();
                $table->string('urgency')->nullable(); // low, medium, high
                $table->dateTime('scheduled_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('cascade');
                $table->integer('sender_id');
                $table->foreign('sender_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->integer('receiver_id');
                $table->foreign('receiver_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->text('content');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('communities')) {
            Schema::create('communities', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->text('content'); // Post content
                $table->string('media_url')->nullable();
                $table->enum('status', ['active', 'flagged', 'hidden'])->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('community_comments')) {
            Schema::create('community_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
                $table->integer('user_id');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->text('comment');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('resources')) {
            Schema::create('resources', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('type'); // video, pdf, audio
                $table->string('category')->nullable();
                $table->string('url');
                $table->text('description')->nullable();
                $table->integer('uploaded_by')->nullable();
                $table->foreign('uploaded_by')->references('user_id')->on('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
                $table->integer('payer_id');
                $table->foreign('payer_id')->references('user_id')->on('users');
                $table->integer('payee_id');
                $table->foreign('payee_id')->references('user_id')->on('users');
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('accessibility_settings')) {
            Schema::create('accessibility_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->string('font_size')->default('medium');
                $table->boolean('tts_enabled')->default(false);
                $table->boolean('stt_enabled')->default(false);
                $table->boolean('high_contrast')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('accessibility_settings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('communities');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('trusted_contacts');
    }
};
