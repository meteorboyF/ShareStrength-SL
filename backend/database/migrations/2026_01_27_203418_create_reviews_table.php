<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('reviewer_id'); // User who reviews
            $table->unsignedBigInteger('reviewee_id'); // Helper being reviewed
            $table->integer('rating')->default(5); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            // Assuming reviewer is User and reviewee is Helper for now, or generic users table
            // We'll keep foreign keys loose or strictly typed if we are sure of IDs.
            // Since we have separate tables but IDs might be assumed from User model context.
            // Let's just index them for now to avoid strict foreign key constraints if tables vary.
            $table->index('reviewer_id');
            $table->index('reviewee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
