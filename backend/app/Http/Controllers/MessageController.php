<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // Get messages between current user and another user (or related to a task)
        $userId = Auth::id();
        $query = Message::query()
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            });

        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->has('contact_id')) {
            $contactId = $request->contact_id;
            $query->where(function ($q) use ($contactId) {
                $q->where('sender_id', $contactId)
                    ->orWhere('receiver_id', $contactId);
            });
        }

        return response()->json($query->with(['sender', 'receiver'])->latest()->paginate(50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'task_id' => $validated['task_id'] ?? null,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        return response()->json($message, 201);
    }

    public function show($id)
    {
        $message = Message::findOrFail($id);
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $message->delete();
        return response()->json(['message' => 'Message deleted']);
    }
}
