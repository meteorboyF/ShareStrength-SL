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
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('trusted-contacts', TrustedContactController::class)->only(['index', 'store']);
        Route::apiResource('messages', MessageController::class)->except(['update']);
        Route::apiResource('communities', CommunityController::class);
        Route::apiResource('resources', \App\Http\Controllers\ResourceController::class);
        Route::apiResource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'store']);

        // Matching
        Route::get('/tasks/{id}/match', [MatchingController::class, 'matchCaregivers']);
    });

    // Public Test Route
    Route::get('/test', function () {
        return response()->json(['status' => 'API is working']);
    });
});
