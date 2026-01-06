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
}
