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
        $query = Task::with(['creator', 'hiring_decision.helper']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Returns all tasks, in production would paginate and filter by visibility
        return response()->json($query->latest()->get());
    }

    // Get tasks created by the authenticated user
    public function myTasks()
    {
        // If user is a helper, they might want to see tasks they are assigned to?
        // But the endpoint name implies 'created by me'.
        // We stick to created_by here.
        return response()->json(Task::with(['creator', 'hiring_decision.helper'])->where('user_id', Auth::id())->latest()->get());
    }

    // Start task
    public function start(Request $request, $id)
    {
        $task = Task::with(['creator', 'hiring_decision.helper'])->findOrFail($id);

        // Authorization: Must be the assigned caregiver
        // Check hiring decision
        $assignedHelperId = $task->hiring_decision?->selected_helper_id;
        
        // Auth::id() is current user/helper ID
        // If logged in as Helper, ID matches helper_id.
        if ($assignedHelperId != Auth::id()) {
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

    // Complete task
    public function complete(Request $request, $id)
    {
        $task = Task::with(['creator', 'hiring_decision.helper'])->findOrFail($id);

        // Authorization: Must be the assigned caregiver
        $assignedHelperId = $task->hiring_decision?->selected_helper_id;

        if ($assignedHelperId != Auth::id()) {
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
            'hourly_rate' => 'nullable|numeric',
            'skill_required' => 'nullable|string',
            'urgency' => 'nullable|string|in:Low,Medium,High',
        ]);

        // Ensure budget is treated as hourly rate if user input

        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'skill_required' => $validated['skill_required'] ?? null,
            'urgency' => $validated['urgency'] ?? 'Medium',
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
        if ($task->user_id !== Auth::id()) {
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
        if ($task->user_id !== Auth::id()) {
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
        if ($originalTask->user_id !== Auth::id()) {
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
            'hourly_rate' => $originalTask->hourly_rate,
            'location' => $originalTask->location, // Keep location if it exists in model? original DB doesn't seem to have it in task fillable? task fillable has it removed.
            // Wait, Task model fillable removed location.
            'skill_required' => $originalTask->skill_required,
            'urgency' => $originalTask->urgency,
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
