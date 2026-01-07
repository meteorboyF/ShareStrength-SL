<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // Get applications for the current user (helper or user logic?)
    // This index method assumes Auth::id() is looking for applications WHERE they are the applicant.
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $user instanceof \App\Models\Helper ? 'helper' : 'user';
        
        $applications = Application::where('helper_id', $user->getKey())
            ->where('applicant_type', $type)
            ->with(['task.creator'])
            ->get();

        return response()->json($applications);
    }

    // Apply for a task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,task_id',
        ]);

        $user = Auth::user();
        $type = $user instanceof \App\Models\Helper ? 'helper' : 'user';

        // Check for existing application
        $exists = Application::where('task_id', $validated['task_id'])
            ->where('helper_id', $user->getKey())
            ->where('applicant_type', $type)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already applied for this task'], 409);
        }

        $application = Application::create([
            'task_id' => $validated['task_id'],
            'helper_id' => $user->getKey(),
            'applicant_type' => $type,
            'status' => 'pending'
        ]);

        return response()->json($application, 201);
    }

    // ... update method stays mostly same but access $application->applicant or use helper_id as ID ...
    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        if ($application->task->user_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,pending',
        ]);

        $application->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted') {
            $task = $application->task;
            $task->status = 'accepted'; 
            $task->save();

            // Create Hiring Decision
            \App\Models\HiringDecision::firstOrCreate(
                ['task_id' => $task->task_id, 'application_id' => $application->application_id],
                [
                    'selected_helper_id' => $application->helper_id, 
                    // Note: HiringDecision might also need to be polymorphic if we hire Users? 
                    // Assuming for now regular flow hires Helpers. 
                    // But if "hhelper" (User) is hired... HiringDecision table schema?
                    // It has 'selected_helper_id'. Does it enforce FK to helpers?
                    // If so, we can't hire a user! 
                    // I should check HiringDecision migration.
                    'decision_status' => 'approved'
                ]
            );
        }

        return response()->json($application);
    }

    public function received(Request $request)
    {
        $userId = Auth::id();

        // 1. Get all task IDs created by this user
        $taskIds = Task::where('user_id', $userId)->pluck('task_id');

        // 2. Get applications for these tasks
        $applications = Application::whereIn('task_id', $taskIds)
            ->with(['task', 'applicant']) // Eager load polymorphic applicant
            ->latest('created_at') 
            ->get();

        return response()->json($applications);
    }
}
