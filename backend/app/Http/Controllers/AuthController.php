<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $accountType = $validated['account_type'];

        $email = $validated['email'];
        if (User::where('email', $email)->exists() || Helper::where('email', $email)->exists()) {
            return response()->json([
                'message' => 'Email already in use.'
            ], 422);
        }

        if ($accountType === 'helpmate') {
            $user = Helper::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'skills' => $validated['skills'] ?? null,
                'is_verified' => false,
            ]);
        } else {
            $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'skills' => $validated['skills'] ?? null,
            'disability_type' => $validated['disability_type'] ?? null,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
            'account_type' => $accountType,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $email = $credentials['email'];
        $password = $credentials['password'];

        // Define models to check
        $guards = [
            'pwd' => User::class,
            'helpmate' => Helper::class,
            'admin' => \App\Models\Admin::class,
        ];

        foreach ($guards as $type => $modelClass) {
            $user = $modelClass::where('email', $email)->first();
            
            if ($user && Hash::check($password, $user->password)) {
                // Check active status
                if (property_exists($user, 'is_active') && !$user->is_active) {
                    return response()->json(['message' => 'Account is inactive'], 403);
                }

                // Check verification for HelpMates
                if ($type === 'helpmate' && !$user->is_verified) {
                    return response()->json(['message' => 'HelpMate account pending verification'], 403);
                }

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token,
                    'account_type' => $type, // Return detected type
                ]);
            }
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
        $accountType = 'pwd'; // Default

        if ($user instanceof Helper) {
            $accountType = 'helpmate';
        } elseif ($user instanceof \App\Models\Admin) {
            $accountType = 'admin';
        }

        // Return user data merged with account_type
        return response()->json(array_merge(
            $user->toArray(),
            ['account_type' => $accountType, 'role' => $accountType] // adding role for frontend compatibility
        ));
    }

}
