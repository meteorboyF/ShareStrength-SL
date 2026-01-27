<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Get public profile of any user
    public function show($id)
    {
        $user = User::with('trustedContacts')->findOrFail($id);

        // Return public profile data
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
            'location' => $user->location,
            'disability_type' => $user->disability_type,
            'skills' => $user->skills,
            'bio' => $user->bio,
            'profile_photo' => $user->profile_photo,
            'profile_photo_url' => $user->profile_photo_url,
            'rating' => $user->rating ?? 0,
            'total_earnings' => $user->total_earnings ?? 0,
            'completed_jobs' => $user->completed_jobs ?? 0,
            'created_at' => $user->created_at,
            'trusted_contacts' => $user->trustedContacts->map(function($contact) {
                return [
                    'id' => $contact->id,
                    'contact_name' => $contact->contact_name,
                    'relation' => $contact->relation,
                    'contact_email' => $contact->contact_email,
                    'contact_phone' => $contact->contact_phone,
                    'status' => $contact->status,
                ];
            }),
        ]);
    }

    // Update authenticated user's profile
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can update this profile'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
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
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can update this profile'], 403);
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $user->profile_photo_url = '/storage/' . $path;
            $user->profile_photo = $user->profile_photo_url;
            $user->save();

            return response()->json([
                'message' => 'Photo uploaded successfully',
                'photo_url' => $user->profile_photo_url
            ]);
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }
}
