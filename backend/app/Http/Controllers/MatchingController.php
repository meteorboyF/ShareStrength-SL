<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Helper;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    /**
     * Find potential caregivers for a specific task.
     */
    public function matchCaregivers(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        // Find available helpers
        $query = Helper::query();

        // Filter by location (simple string match for now)
        if ($task->location) {
            // In production, use geospatial search
            $query->where('address', 'LIKE', "%{$task->location}%");
        }

        // Filter by skills (if caregiver has skills matching any of task requirements)
        if (!empty($task->required_skills)) {
            $requiredSkills = $task->required_skills; // Array

            // Simple approach: filter where 'skills' text contains one of the required skills
            $query->where(function ($q) use ($requiredSkills) {
                foreach ($requiredSkills as $skill) {
                    $q->orWhere('skills', 'LIKE', "%{$skill}%");
                }
            });
        }

        $caregivers = $query->paginate(20);

        return response()->json([
            'task' => $task,
            'matches' => $caregivers
        ]);
    }
}
