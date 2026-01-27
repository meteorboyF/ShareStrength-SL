<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // List tasks
    public function index(Request $request)
    {
        // Simple filter by status
        $query = Task::with(['creator', 'caregiver']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Returns all tasks, in production would paginate and filter by visibility
        return response()->json($query->latest()->get());
    }

    // Get tasks created by the authenticated user
    public function myTasks()
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can view their tasks'], 403);
        }

        return response()->json(
            Task::with(['creator', 'caregiver'])
                ->where('created_by', $user->getKey())
                ->latest()
                ->get()
        );
    }

    // Accept task (as Helper)
    public function accept(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = $request->user();

        if (!$user instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can accept tasks'], 403);
        }

        // Authorization: Must be the assigned caregiver
        if ($task->caregiver_id !== $user->getKey()) {
            return response()->json([
                'message' => 'Unauthorized. You are not assigned to this task.'
            ], 403);
        }

        // Validation: Task must be in 'requested' status
        if ($task->status !== 'requested') {
            return response()->json([
                'message' => 'Cannot accept task. Current status: ' . $task->status . '. Expected: requested'
            ], 400);
        }

        $task->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Task accepted successfully',
            'task' => $task
        ]);
    }

    // Start task
    public function start(Request $request, $id)
    {
        $task = Task::with(['creator', 'caregiver'])->findOrFail($id);
        $user = $request->user();

        if (!$user instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can start tasks'], 403);
        }

        // Authorization: Must be the assigned caregiver
        if ($task->caregiver_id !== $user->getKey()) {
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

    // Pause task
    public function pause(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = $request->user();

        if ($task->caregiver_id !== $user->getKey()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->status !== 'in_progress') {
            return response()->json(['message' => 'Task not in progress'], 400);
        }

        // Calculate time since last start
        $accumulated = $task->accumulated_seconds ?? 0;
        if ($task->started_at) {
            $start = \Carbon\Carbon::parse($task->started_at);
            $seconds = $start->diffInSeconds(now());
            $accumulated += $seconds;
        }

        $task->update([
            'status' => 'paused',
            'started_at' => null,
            'accumulated_seconds' => $accumulated
        ]);

        return response()->json(['message' => 'Task paused', 'task' => $task]);
    }

    // Resume task
    public function resume(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = $request->user();

        if ($task->caregiver_id !== $user->getKey()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->status !== 'paused') {
            return response()->json(['message' => 'Task not paused'], 400);
        }

        $task->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json(['message' => 'Task resumed', 'task' => $task]);
    }

    // Complete task
    public function complete(Request $request, $id)
    {
        $task = Task::with(['creator', 'caregiver'])->findOrFail($id);
        $user = $request->user();

        if (!$user instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can complete tasks'], 403);
        }

        // Authorization: Must be the assigned caregiver
        if ($task->caregiver_id !== $user->getKey()) {
            return response()->json([
                'message' => 'Unauthorized. You are not assigned to this task.'
            ], 403);
        }

        // Calculate final accumulated time
        $finalSeconds = $task->accumulated_seconds ?? 0;
        
        if ($task->status === 'in_progress' && $task->started_at) {
            $start = \Carbon\Carbon::parse($task->started_at);
            $finalSeconds += $start->diffInSeconds(now());
        }

        // Update task status and timestamp
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
            'accumulated_seconds' => $finalSeconds,
            'started_at' => null // Clear current start time
        ]);

        // Calculate hours with decimal precision
        $hours = $finalSeconds / 3600;

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
            'hours_worked' => $hours,
            'hourly_rate' => $hourlyRate,
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
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can create tasks'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric',
            'hourly_rate' => 'nullable|numeric',
            'required_skills' => 'nullable',
            'skill_required' => 'nullable|string',
            'urgency' => 'nullable|string|in:low,medium,high,Low,Medium,High',
            'location' => 'nullable|string|max:255',
        ]);
        $budget = $validated['budget'] ?? $validated['hourly_rate'] ?? null;
        $requiredSkills = $validated['required_skills'] ?? null;
        if (is_string($requiredSkills)) {
            $requiredSkills = array_values(array_filter(array_map('trim', explode(',', $requiredSkills))));
        }
        if (!$requiredSkills && !empty($validated['skill_required'])) {
            $requiredSkills = [$validated['skill_required']];
        }
        $urgency = strtolower($validated['urgency'] ?? 'medium');

        $task = Task::create([
            'created_by' => $user->getKey(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'budget' => $budget,
            'required_skills' => $requiredSkills,
            'urgency' => $urgency,
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
        $user = $request->user();

        // Basic authorization check
        if (!$user instanceof User || $task->created_by !== $user->getKey()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'in:open,requested,accepted,in_progress,completed,cancelled',
            'title' => 'string|max:255',
            'description' => 'string',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();
        if (!$user instanceof User || $task->created_by !== $user->getKey()) {
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
        $user = $request->user();
        if (!$user instanceof User || $originalTask->created_by !== $user->getKey()) {
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
            'created_by' => $user->getKey(),
            'title' => $originalTask->title,
            'description' => $originalTask->description,
            'budget' => $originalTask->budget,
            'location' => $originalTask->location,
            'required_skills' => $originalTask->required_skills,
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
