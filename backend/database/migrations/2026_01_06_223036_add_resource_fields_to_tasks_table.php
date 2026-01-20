<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('task_type', ['general', 'resource_creation'])->default('general')->after('status');
            $table->enum('resource_type', ['audiobook', 'sign_language_video', 'braille', 'large_print', 'accessible_pdf', 'other'])->nullable()->after('task_type');
            $table->string('resource_title')->nullable()->after('resource_type');
            $table->string('resource_file_url')->nullable()->after('resource_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['task_type', 'resource_type', 'resource_title', 'resource_file_url']);
        });
    }
};
