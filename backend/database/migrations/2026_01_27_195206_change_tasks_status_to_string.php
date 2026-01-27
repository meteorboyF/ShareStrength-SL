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
        Schema::table('tasks', function (Blueprint $table) {
            // Change enum to string to avoid duplication/truncation issues
            $table->string('status')->default('open')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert to enum (be careful with existing data)
            // $table->enum('status', ['open', 'requested', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('open')->change();
        });
    }
};
