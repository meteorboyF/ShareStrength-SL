<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Get public profile of any user
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Return public profile data
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'location' => $user->location,
            'disability_type' => $user->disability_type,
            'skills' => $user->skills,
            'bio' => $user->bio,
            'profile_photo_url' => $user->profile_photo_url,
            'rating' => $user->rating ?? 0,
            'total_earnings' => $user->total_earnings ?? 0,
            'completed_jobs' => $user->completed_jobs ?? 0,
            'created_at' => $user->created_at,
        ]);
    }

    // Update authenticated user's profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'location' => 'sometimes|string|max:255',
            'disability_type' => 'sometimes|nullable|string|max:255',
            'skills' => 'sometimes|nullable|string',
            'bio' => 'sometimes|nullable|string|max:1000',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    // Upload profile photo
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $user->profile_photo_url = '/storage/' . $path;
            $user->save();

            return response()->json([
                'message' => 'Photo uploaded successfully',
                'photo_url' => $user->profile_photo_url
            ]);
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }
}
