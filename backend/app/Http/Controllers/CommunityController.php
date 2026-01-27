<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function index()
    {
        // Return active posts
        return response()->json(Community::with('user')->where('status', 'active')->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can post'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'media_url' => 'nullable|string', // In real app, handle file upload here
        ]);

        $post = Community::create([
            'user_id' => $user->getKey(),
            'content' => $validated['content'],
            'media_url' => $validated['media_url'] ?? null,
            'status' => 'active',
        ]);

        return response()->json($post, 201);
    }

    public function show($id)
    {
        return response()->json(Community::with(['user', 'comments.user'])->findOrFail($id));
    }

    public function destroy($id)
    {
        $post = Community::findOrFail($id);
        $user = Auth::user();
        if (!$user instanceof User || $post->user_id !== $user->getKey()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted']);
    }
}
