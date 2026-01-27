<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Helper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function registerHelper(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:helpers,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'skills' => 'required|string',
            'password' => 'required|string|min:6',
            'skill_verification_doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Handle optional file upload
        $skillDocPath = null;
        if ($request->hasFile('skill_verification_doc')) {
            $skillDocPath = $request->file('skill_verification_doc')->store('skill_docs', 'public');
        }

        // 3. Create the Helper
        $helper = Helper::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone,
            'address' => $request->address,
            'skills' => $request->skills,
            // Note: You would add a column to your DB for 'skill_document_path' to save $skillDocPath
        ]);

        // 4. Return success response
        return response()->json([
            'message' => 'Helper registration successful! Your account will be activated after admin verification.',
            'helper' => $helper
        ], 201);
    }

    /**
     * Handle a registration request for a new User.
     */
    public function registerUser(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'user_type' => 'required|string|in:disabled_individual,caretaker',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Create the User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone,
            'address' => $request->address,
            'user_type' => $request->user_type,
        ]);

        // 3. Return success response
        return response()->json([
            'message' => 'User registration successful! Once verified by an admin you can log in.',
            'user' => $user
        ], 201);
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Try to authenticate as a User first
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'role' => 'user'
            ]);
        }

        // If not a user, try to authenticate as a Helper
        $helper = Helper::where('email', $request->email)->first();
        if ($helper && Hash::check($request->password, $helper->password)) {
            $token = $helper->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $helper,
                'role' => 'helpmate'
            ]);
        }

        // If neither, authentication failed
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        // Define validation rules based on user type
        $rules = [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];

        if ($user instanceof \App\Models\Helper) {
            $rules['skills'] = 'nullable|string';
            $rules['price_per_hour'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Update the user
        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}