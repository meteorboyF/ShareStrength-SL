<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // List tasks
    public function index(Request $request)
    {
        // Simple filter by status
        $query = Task::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Returns all tasks, in production would paginate and filter by visibility
        return response()->json($query->with(['creator', 'caregiver'])->latest()->paginate(20));
    }

    // Create task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric',
            'scheduled_at' => 'nullable|date',
            'location' => 'nullable|string',
        ]);

        $task = Task::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget' => $validated['budget'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'location' => $validated['location'] ?? null,
            'status' => 'open',
        ]);

        return response()->json($task, 201);
    }

    public function show($id)
    {
        return response()->json(Task::with(['creator', 'caregiver', 'messages'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Basic authorization check
        if ($task->created_by !== Auth::id() && $task->caregiver_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'in:open,requested,accepted,completed,cancelled',
            'title' => 'string|max:255',
            'description' => 'string',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        if ($task->created_by !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
