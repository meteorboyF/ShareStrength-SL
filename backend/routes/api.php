<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ConversationController;
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

    // Shop Routes (Public)
    Route::apiResource('products', \App\Http\Controllers\ProductController::class)->only(['index', 'show']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Feature Routes
        Route::get('my-tasks', [TaskController::class, 'myTasks']);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('trusted-contacts', TrustedContactController::class)->only(['index', 'store']);
        Route::apiResource('messages', MessageController::class)->except(['update']);
        Route::patch('messages/{id}/read', [MessageController::class, 'markAsRead']);
        
        Route::apiResource('conversations', ConversationController::class)->only(['index', 'show']);
        Route::post('conversations/get-or-create', [ConversationController::class, 'getOrCreate']);
        Route::patch('conversations/{id}/read', [MessageController::class, 'markConversationAsRead']);
        
        Route::apiResource('communities', CommunityController::class);
        Route::apiResource('resources', \App\Http\Controllers\ResourceController::class);
        Route::apiResource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'store']);
        Route::get('/applications/received', [\App\Http\Controllers\ApplicationController::class, 'received']);
        Route::apiResource('applications', \App\Http\Controllers\ApplicationController::class)->only(['index', 'store', 'update']);

        Route::post('orders', [\App\Http\Controllers\OrderController::class, 'store']);

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

    Route::get('/debug/schema', function () {
        try {
            $products = \Illuminate\Support\Facades\DB::select('DESCRIBE products');
            $orders = \Illuminate\Support\Facades\DB::select('DESCRIBE orders');
            return [
                'products' => $products,
                'orders' => $orders,
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    });

    Route::get('/debug/seed', function () {
        try {
            $seeder = new \Database\Seeders\ProductSeeder();
            $seeder->run();
            return ['status' => 'Seeding successful', 'products' => \App\Models\Product::all()];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }
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
