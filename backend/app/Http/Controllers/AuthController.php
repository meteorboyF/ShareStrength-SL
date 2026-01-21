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

        $accountType = $request->input('account_type', 'pwd');
        $model = $accountType === 'helpmate' ? Helper::class : User::class;

        $user = $model::where('email', $credentials['email'])->first();
        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (property_exists($user, 'is_active') && !$user->is_active) {
                return response()->json(['message' => 'Account is inactive'], 403);
            }

            if ($accountType === 'helpmate' && property_exists($user, 'is_verified') && !$user->is_verified) {
                return response()->json(['message' => 'HelpMate account pending verification'], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
                'account_type' => $accountType,
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
        return response()->json($request->user());
    }

}
