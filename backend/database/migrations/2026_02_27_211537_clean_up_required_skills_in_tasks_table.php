<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Task;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This is a data-only migration, so we don't change the schema.
        // We just clean the existing data.
        Task::all()->each(function ($task) {
            
            // Get the raw string from the database, bypassing any model casting.
            $rawSkills = $task->getRawOriginal('required_skills');

            if (is_null($rawSkills) || empty($rawSkills) || $rawSkills === '[null]') {
                $cleanedSkills = [];
            } else {
                // First decode attempt
                $skills = json_decode($rawSkills, true);

                // If it's still a string, it was double-encoded, so decode it AGAIN.
                if (is_string($skills)) {
                    $skills = json_decode($skills, true);
                }
                
                // If it's still not an array after all that, default to an empty array.
                $cleanedSkills = is_array($skills) ? $skills : [];
            }

            // Update the task with the clean array, which will be saved as proper JSON.
            $task->required_skills = $cleanedSkills;
            $task->saveQuietly(); // Use saveQuietly to avoid firing any model events.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a one-way data cleanup, so no down action is needed.
    }
};