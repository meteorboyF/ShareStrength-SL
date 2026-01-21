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
            $table->foreignId('caregiver_id')->nullable()->constrained('helpers')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->enum('status', ['open', 'requested', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->json('required_skills')->nullable();
            $table->string('urgency')->nullable(); // low, medium, high
            $table->dateTime('scheduled_at')->nullable();
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
            $table->string('type'); // audiobook, sign_language_video, braille, large_print, accessible_pdf, other
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('file_url')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->string('language')->nullable();
            $table->string('author')->nullable();
            $table->string('narrator')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('payer_id')->constrained('users');
            $table->foreignId('payee_id')->constrained('helpers');
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
