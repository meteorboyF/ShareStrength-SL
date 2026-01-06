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
        return response()->json($query->with(['creator', 'caregiver'])->latest()->get());
    }

    // Get tasks created by the authenticated user
    public function myTasks()
    {
        return response()->json(Task::where('created_by', Auth::id())->with(['creator', 'caregiver'])->latest()->get());
    }

    // Start task
    public function start(Request $request, $id)
    {
        $task = Task::with(['creator', 'caregiver'])->findOrFail($id);

        // Authorization: Must be the assigned caregiver
        if ($task->caregiver_id != Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You are not assigned to this task.'
            ], 403);
        }

        // Validation: Task must be in 'accepted' status
        if ($task->status !== 'accepted') {
            return response()->json([
                'message' => 'Cannot start task. Current status: ' . $task->status . '. Expected: accepted'
            ], 400);
        }

        // Update task status and timestamp
        $task->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Task started successfully',
            'task' => $task
        ]);
    }

    // Complete task and calculate payment
    public function complete(Request $request, $id)
    {
        $task = Task::with(['creator', 'caregiver'])->findOrFail($id);

        // Authorization: Must be the assigned caregiver
        if ($task->caregiver_id != Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You are not assigned to this task.'
            ], 403);
        }

        // Validation: Task must be in 'in_progress' status
        if ($task->status !== 'in_progress') {
            return response()->json([
                'message' => 'Cannot complete task. Current status: ' . $task->status . '. Expected: in_progress'
            ], 400);
        }

        // Validation: Must have started_at timestamp
        if (!$task->started_at) {
            return response()->json([
                'message' => 'Cannot complete task. No start time recorded.'
            ], 400);
        }

        // Update task status and timestamp
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Calculate payment based on time worked
        $start = \Carbon\Carbon::parse($task->started_at);
        $end = \Carbon\Carbon::parse($task->completed_at);

        // Calculate hours with decimal precision
        $totalMinutes = $start->diffInMinutes($end);
        $hours = $totalMinutes / 60;

        // Round up to nearest 0.5 hour, minimum 0.5 hours
        $hours = max(0.5, ceil($hours * 2) / 2);

        // Calculate amount (budget is hourly rate)
        $hourlyRate = $task->budget ?: 15; // Default $15/hr if not set
        $amount = round($hours * $hourlyRate, 2);

        // Create Payment Record
        $payment = \App\Models\Payment::create([
            'task_id' => $task->id,
            'payer_id' => $task->created_by,
            'payee_id' => $task->caregiver_id,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Refresh task to include updated data
        $task->refresh();

        return response()->json([
            'message' => 'Task completed successfully',
            'task' => $task,
            'payment' => [
                'amount' => $amount,
                'hours_worked' => $hours,
                'hourly_rate' => $hourlyRate,
                'status' => 'pending'
            ]
        ]);
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

        // Ensure budget is treated as hourly rate if user input

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

    // Repost a task (create new task from existing one)
    public function repost(Request $request, $id)
    {
        $originalTask = Task::findOrFail($id);

        // Authorization: Must be the task creator
        if ($originalTask->created_by !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only allow reposting completed or cancelled tasks
        if (!in_array($originalTask->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Can only repost completed or cancelled tasks'
            ], 400);
        }

        // Create new task with same details
        $newTask = Task::create([
            'created_by' => Auth::id(),
            'title' => $originalTask->title,
            'description' => $originalTask->description,
            'budget' => $originalTask->budget,
            'location' => $originalTask->location,
            'required_skills' => $originalTask->required_skills,
            'urgency' => $originalTask->urgency,
            'scheduled_at' => $originalTask->scheduled_at,
            'status' => 'open',
            'caregiver_id' => null,
            'started_at' => null,
            'completed_at' => null,
        ]);

        return response()->json([
            'message' => 'Task reposted successfully',
            'task' => $newTask
        ], 201);
    }
}
