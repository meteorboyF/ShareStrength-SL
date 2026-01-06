<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrustedContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Feature Routes
        Route::get('my-tasks', [TaskController::class, 'myTasks']);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('trusted-contacts', TrustedContactController::class)->only(['index', 'store']);
        Route::apiResource('messages', MessageController::class)->except(['update']);
        Route::apiResource('communities', CommunityController::class);
        Route::apiResource('resources', \App\Http\Controllers\ResourceController::class);
        Route::apiResource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'store']);
        Route::get('/applications/received', [\App\Http\Controllers\ApplicationController::class, 'received']);
        Route::apiResource('applications', \App\Http\Controllers\ApplicationController::class)->only(['index', 'store', 'update']);

        // Matching
        Route::put('/tasks/{id}/start', [TaskController::class, 'start']);
        Route::put('/tasks/{id}/complete', [TaskController::class, 'complete']);
        Route::post('/tasks/{id}/repost', [TaskController::class, 'repost']);
        Route::get('/tasks/{id}/match', [MatchingController::class, 'matchCaregivers']);
    });

    // Public Test Route
    Route::get('/test', function () {
        return response()->json(['status' => 'API is working']);
    });

    Route::get('/fix-db', function () {
        $results = [];
        try {
            // Fix 1: Change status column to VARCHAR
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(50) DEFAULT 'open'");
            $results[] = 'Status column updated to VARCHAR(50)';
        } catch (\Exception $e) {
            $results[] = 'Status column error: ' . $e->getMessage();
        }

        try {
            // Fix 2: Add started_at column if not exists
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tasks ADD COLUMN started_at TIMESTAMP NULL");
            $results[] = 'started_at column added';
        } catch (\Exception $e) {
            $results[] = 'started_at: ' . $e->getMessage();
        }

        try {
            // Fix 3: Add completed_at column if not exists
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tasks ADD COLUMN completed_at TIMESTAMP NULL");
            $results[] = 'completed_at column added';
        } catch (\Exception $e) {
            $results[] = 'completed_at: ' . $e->getMessage();
        }

        return response()->json(['results' => $results]);
    });
});
