<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('resources');

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uploaded_by');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path', 500);
            $table->string('file_type', 50)->nullable();
            $table->string('type', 50)->nullable(); // e.g. pdf, video, audio
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('file_url', 500)->nullable(); // Redundant with file_path but requested
            $table->integer('file_size')->nullable(); // in bytes
            $table->integer('duration')->nullable(); // in seconds
            $table->string('language')->nullable();
            $table->string('author')->nullable();
            $table->string('narrator')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('download_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('task_id')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
