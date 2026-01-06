<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    // Get applications for the current user (helper)
    public function index(Request $request)
    {
        $userId = Auth::id();
        $applications = Application::where('helper_id', $userId)
            ->with(['task.creator']) // Eager load task and its creator
            ->get();

        return response()->json($applications);
    }

    // Apply for a task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id', // Assuming task primary key is 'id' now, need to be careful with 'task_id' legacy
        ]);

        // Check for existing application
        $exists = Application::where('task_id', $validated['task_id'])
            ->where('helper_id', Auth::id())
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'You have already applied for this task'], 409);
        }

        $application = Application::create([
            'task_id' => $validated['task_id'],
            'helper_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return response()->json($application, 201);
    }

    // Get applications received for tasks created by the current user
    // Update application status (e.g. Accept/Reject)
    public function update(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        // Authorization check: User must own the task
        if ($application->task->created_by != Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. Task Creator ID: ' . $application->task->created_by . ' vs Your ID: ' . Auth::id()
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,pending',
        ]);

        $application->update(['status' => $validated['status']]);

        // If accepted, we might want to update the task status too
        if ($validated['status'] === 'accepted') {
            $task = $application->task;
            $task->status = 'accepted'; // Changed from 'assigned' to match Enum and frontend logic
            $task->caregiver_id = $application->helper_id;
            $task->save();


            // Optional: Reject other pending applications for this task?
        }

        return response()->json($application);
    }

    public function received(Request $request)
    {
        $userId = Auth::id();

        // 1. Get all task IDs created by this user
        $taskIds = Task::where('created_by', $userId)->pluck('id');

        // 2. Get applications for these tasks
        $applications = Application::whereIn('task_id', $taskIds)
            ->with(['task', 'helper'])
            ->latest('id') // useful to see newest first
            ->get();

        return response()->json($applications);
    }
}
