<?php

namespace App\Http\Controllers;

use App\Models\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelperController extends Controller
{
    public function show($id)
    {
        $helper = Helper::findOrFail($id);

        return response()->json([
            'id' => $helper->id,
            'name' => $helper->name,
            'email' => $helper->email,
            'phone' => $helper->phone,
            'location' => $helper->location,
            'skills' => $helper->skills,
            'bio' => $helper->bio,
            'profile_photo_url' => $helper->profile_photo_url,
            'is_verified' => (bool) $helper->is_verified,
            'created_at' => $helper->created_at,
        ]);
    }

    public function update(Request $request)
    {
        $helper = Auth::user();
        if (!$helper instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can update this profile'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:helpers,email,' . $helper->id,
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'location' => 'sometimes|string|max:255',
            'skills' => 'sometimes|nullable|string',
            'bio' => 'sometimes|nullable|string|max:1000',
        ]);

        $helper->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'helper' => $helper,
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $helper = Auth::user();
        if (!$helper instanceof Helper) {
            return response()->json(['message' => 'Only HelpMates can update this profile'], 403);
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $helper->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $helper->profile_photo_url = '/storage/' . $path;
            $helper->profile_photo = $helper->profile_photo_url;
            $helper->save();

            return response()->json([
                'message' => 'Photo uploaded successfully',
                'photo_url' => $helper->profile_photo_url
            ]);
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }
}
