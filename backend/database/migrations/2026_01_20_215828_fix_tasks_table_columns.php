<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, expand the status enum to include all values (including old and new)
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('open', 'in_progress', 'requested', 'accepted', 'completed', 'cancelled') DEFAULT 'open'");
        
        // Update existing status values to match new enum
        DB::statement("UPDATE tasks SET status = 'accepted' WHERE status = 'in_progress'");
        
        // Now remove the old 'in_progress' value from enum
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('open', 'requested', 'accepted', 'completed', 'cancelled') DEFAULT 'open'");
        
        Schema::table('tasks', function (Blueprint $table) {
            // Rename user_id to created_by if it exists
            if (Schema::hasColumn('tasks', 'user_id')) {
                $table->renameColumn('user_id', 'created_by');
            }
            
            // Add missing columns
            if (!Schema::hasColumn('tasks', 'caregiver_id')) {
                $table->unsignedBigInteger('caregiver_id')->nullable()->after('created_by');
            }
            
            if (!Schema::hasColumn('tasks', 'location')) {
                $table->string('location')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('tasks', 'budget')) {
                $table->decimal('budget', 10, 2)->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('tasks', 'required_skills')) {
                $table->json('required_skills')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('tasks', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable()->after('urgency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'scheduled_at')) {
                $table->dropColumn('scheduled_at');
            }
            if (Schema::hasColumn('tasks', 'required_skills')) {
                $table->dropColumn('required_skills');
            }
            if (Schema::hasColumn('tasks', 'budget')) {
                $table->dropColumn('budget');
            }
            if (Schema::hasColumn('tasks', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('tasks', 'caregiver_id')) {
                $table->dropColumn('caregiver_id');
            }
            if (Schema::hasColumn('tasks', 'created_by')) {
                $table->renameColumn('created_by', 'user_id');
            }
        });
    }
};
