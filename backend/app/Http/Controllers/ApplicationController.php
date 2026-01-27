<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Helper;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // Get applications for the current user (helper or user logic?)
    // This index method assumes Auth::id() is looking for applications WHERE they are the applicant.
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can view applications'], 403);
        }

        $applications = Application::where('helper_id', $user->getKey())
            ->where('applicant_type', 'helper')
            ->with(['task.creator'])
            ->get();

        return response()->json($applications);
    }

    // Apply for a task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        $user = $request->user();
        if (!$user instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can apply to tasks'], 403);
        }

        // Check for existing application
        $exists = Application::where('task_id', $validated['task_id'])
            ->where('helper_id', $user->getKey())
            ->where('applicant_type', 'helper')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already applied for this task'], 409);
        }

        $application = Application::create([
            'task_id' => $validated['task_id'],
            'helper_id' => $user->getKey(),
            'applicant_type' => 'helper',
            'status' => 'pending'
        ]);

        return response()->json($application, 201);
    }

    // ... update method stays mostly same but access $application->applicant or use helper_id as ID ...
    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $user = $request->user();
        if (!$user instanceof User || $application->task->created_by != $user->getKey()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,pending',
        ]);

        $application->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted') {
            $task = $application->task;
            $task->status = 'accepted'; 
            $task->caregiver_id = $application->helper_id;
            $task->save();
        }

        return response()->json($application);
    }

    public function received(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can view received applications'], 403);
        }
        $userId = $user->getKey();

        // 1. Get all task IDs created by this user
        $taskIds = Task::where('created_by', $userId)->pluck('id');

        // 2. Get applications for these tasks
        $applications = Application::whereIn('task_id', $taskIds)
            ->with(['task.creator', 'applicant'])
            ->latest('created_at') 
            ->get();

        return response()->json($applications);
    }
}
