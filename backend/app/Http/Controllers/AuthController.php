<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Helper; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'user_type' => 'disabled_individual',
            'status' => 'active',
        ]);

        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    }

    public function registerHelper(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:helpers,email',
            'password' => 'required|string|min:6',
            'skills' => 'required|array'
        ]);

        $helper = Helper::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'skills' => implode(', ', $request->skills), 
            'status' => 'active',
            'verification_status' => 'pending',
        ]);

        return response()->json(['message' => 'HelpMate registered successfully!', 'helper' => $helper], 201);
    }
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 1. Search in the 'users' table
    $user = \App\Models\User::where('email', $request->email)->first();
    $role = 'user';

    // 2. If not found, search in the 'helpers' table
    if (!$user) {
        $user = \App\Models\Helper::where('email', $request->email)->first();
        $role = 'helper';
    }

    // 3. DEBUG: Check if user exists
    if (!$user) {
        return response()->json(['message' => 'Email not found in our records.'], 401);
    }

    // 4. CRITICAL: Check the password using Hash::check
    // We compare the typed password ($request->password) 
    // with the scrambled one in the DB ($user->password_hash)
    if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password_hash)) {
        return response()->json(['message' => 'Password does not match.'], 401);
    }

    // 5. SUCCESS
    return response()->json([
        'message' => 'Login successful!',
        'user' => $user,
        'role' => $role
    ]);
}
}
