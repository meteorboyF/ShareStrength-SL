<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trusted_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trusted_user_id')->nullable()->constrained('users')->onDelete('set null'); // If linked to platform user
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['pending', 'verified'])->default('pending');
            $table->string('verification_token')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('caregiver_id')->nullable()->constrained('users')->onDelete('set null');
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

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content'); // Post content
            $table->string('media_url')->nullable();
            $table->enum('status', ['active', 'flagged', 'hidden'])->default('active');
            $table->timestamps();
        });

        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // video, pdf, audio
            $table->string('category')->nullable();
            $table->string('url');
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('payer_id')->constrained('users');
            $table->foreignId('payee_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('accessibility_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('font_size')->default('medium');
            $table->boolean('tts_enabled')->default(false);
            $table->boolean('stt_enabled')->default(false);
            $table->boolean('high_contrast')->default(false);
            $table->timestamps();
        });
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
