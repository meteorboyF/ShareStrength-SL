<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        if ($validated['role'] === 'caregiver') {
            // Register as Helper in helpers table
            $helper = \App\Models\Helper::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'phone_number' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'skills' => $validated['skills'] ?? null, // Helpers have skills
                'status' => 'active',
                'verification_status' => 'pending', // Default
            ]);

            $token = $helper->createToken('auth_token')->plainTextToken;
            $helper->role = 'caregiver'; // Add dynamic role property for frontend

            return response()->json([
                'message' => 'Helper registered successfully',
                'user' => $helper,
                'token' => $token,
            ], 201);

        } else {
            // Register as User (PWD/Family) in users table
            
            // Map frontend role to database user_type enum for Users table
            $userTypeMap = [
                'pwd' => 'disabled_individual',
                'family_member' => 'family_member', // Assuming this exists or mapping to pwd
                // 'caregiver' case covered above
            ];

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password_hash' => Hash::make($validated['password']),
                'user_type' => $userTypeMap[$validated['role']] ?? 'disabled_individual',
                'phone_number' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->role = 'pwd'; // Standardize role for frontend

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        
        // 1. Try finding in Users table
        $user = User::where('email', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Map DB user_type to frontend role
            $roleMap = [
                'disabled_individual' => 'pwd',
                'family_member' => 'pwd',
                'caretaker' => 'caregiver', // Legacy support
            ];
            $user->role = $roleMap[$user->user_type] ?? 'pwd';

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ]);
        }

        // 2. Try finding in Helpers table
        $helper = \App\Models\Helper::where('email', $credentials['email'])->first();
        if ($helper && Hash::check($credentials['password'], $helper->password_hash)) {
            $token = $helper->createToken('auth_token')->plainTextToken;
            $helper->role = 'caregiver';

            return response()->json([
                'message' => 'Login successful',
                'user' => $helper,
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        if ($user instanceof \App\Models\Helper) {
            $user->role = 'caregiver';
        } else {
            // It's a User
            $roleMap = [
                'disabled_individual' => 'pwd',
                'family_member' => 'pwd',
                'caretaker' => 'caregiver',
            ];
            $user->role = $roleMap[$user->user_type] ?? 'pwd';
        }

        return response()->json($user);
    }

}
