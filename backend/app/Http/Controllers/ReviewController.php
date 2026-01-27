<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof \App\Models\User) {
            return response()->json(['message' => 'Only users can leave reviews'], 403);
        }

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'reviewee_id' => 'required|exists:helpers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        // Prevent duplicate reviews for same task
        if (\App\Models\Review::where('task_id', $validated['task_id'])->exists()) {
            return response()->json(['message' => 'You have already reviewed this task'], 400);
        }

        $review = \App\Models\Review::create([
            'task_id' => $validated['task_id'],
            'reviewer_id' => $user->getKey(),
            'reviewee_id' => $validated['reviewee_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment']
        ]);

        // Update Helper's average rating
        $helper = \App\Models\Helper::find($validated['reviewee_id']);
        $avgRating = \App\Models\Review::where('reviewee_id', $helper->id)->avg('rating');
        
        $helper->rating = round($avgRating, 1);
        $helper->save();

        return response()->json($review, 201);
    }
}
