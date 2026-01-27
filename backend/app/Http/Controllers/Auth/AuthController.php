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

        // 3. Create the Helper user
        $helper = Helper::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'skills' => $request->skills,
            'is_verified' => false,
            // Note: Add a column for 'skill_document_path' to save $skillDocPath if needed.
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
            'phone' => $request->phone,
            'address' => $request->address,
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
            'account_type' => 'nullable|in:pwd,helpmate',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $accountType = $request->input('account_type', 'pwd');
        $model = $accountType === 'helpmate' ? Helper::class : User::class;
        $user = $model::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'account_type' => $accountType,
            ]);
        }

        // If neither, authentication failed
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
