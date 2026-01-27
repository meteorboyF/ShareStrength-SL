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
        Schema::create('helpers', function (Blueprint $table) {
            $table->id('helper_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->json('skills')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->string('profile_photo')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->unsignedBigInteger('verified_by')->nullable(); // Admin ID who verified
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpers');
    }
};
