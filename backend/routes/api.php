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
        Route::get('/payments/summary', [\App\Http\Controllers\PaymentController::class, 'summary']);
        Route::get('/payments/insights', [\App\Http\Controllers\PaymentController::class, 'insights']);
        Route::get('/applications/received', [\App\Http\Controllers\ApplicationController::class, 'received']);
        Route::apiResource('applications', \App\Http\Controllers\ApplicationController::class)->only(['index', 'store', 'update']);

        // User Profile Routes
        Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\UserController::class, 'update']);
        Route::post('/profile/photo', [\App\Http\Controllers\UserController::class, 'uploadPhoto']);

        // Resource Routes (Public)
        Route::get('/resources', [\App\Http\Controllers\ResourceController::class, 'index']);
        Route::get('/resources/featured', [\App\Http\Controllers\ResourceController::class, 'featured']);
        Route::get('/resources/search', [\App\Http\Controllers\ResourceController::class, 'search']);
        Route::get('/resources/categories', [\App\Http\Controllers\ResourceCategoryController::class, 'index']);
        Route::get('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'show']);
        Route::get('/resources/{id}/download', [\App\Http\Controllers\ResourceController::class, 'download']);

        // Admin-only resource management
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
            Route::post('/resources', [\App\Http\Controllers\ResourceController::class, 'store']);
            Route::put('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'update']);
            Route::delete('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'destroy']);

            // Category management
            Route::post('/categories', [\App\Http\Controllers\ResourceCategoryController::class, 'store']);
            Route::put('/categories/{id}', [\App\Http\Controllers\ResourceCategoryController::class, 'update']);
            Route::delete('/categories/{id}', [\App\Http\Controllers\ResourceCategoryController::class, 'destroy']);
        });

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
            return [
                'users_count' => \App\Models\User::count(),
                'helpers_count' => \App\Models\Helper::count(),
                'conversations_count' => \Illuminate\Support\Facades\DB::table('conversations')->count(),
                'messages_count' => \App\Models\Message::count(),
                'conversations_table_exists' => Schema::hasTable('conversations'),
                'messages_columns' => \Illuminate\Support\Facades\DB::select('DESCRIBE messages'),
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

    Route::get('/fix-messaging', function () {
        $results = [];
        try {
            DB::statement("
                CREATE TABLE IF NOT EXISTS conversations (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_one_id BIGINT UNSIGNED NOT NULL,
                    user_one_type VARCHAR(255) NOT NULL,
                    user_two_id BIGINT UNSIGNED NOT NULL,
                    user_two_type VARCHAR(255) NOT NULL,
                    task_id BIGINT UNSIGNED NULL,
                    last_message_at TIMESTAMP NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    INDEX idx_user_one (user_one_id, user_one_type),
                    INDEX idx_user_two (user_two_id, user_two_type)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            $results[] = 'Conversations table created';
        } catch (\Exception $e) {
            $results[] = 'Conversations: ' . $e->getMessage();
        }

        try {
            DB::statement("ALTER TABLE messages ADD COLUMN conversation_id BIGINT UNSIGNED NULL AFTER id");
            $results[] = 'Added conversation_id';
        } catch (\Exception $e) {
            $results[] = 'conversation_id: ' . $e->getMessage();
        }

        try {
            DB::statement("ALTER TABLE messages ADD COLUMN sender_type VARCHAR(255) NOT NULL DEFAULT 'user' AFTER sender_id");
            $results[] = 'Added sender_type';
        } catch (\Exception $e) {
            $results[] = 'sender_type: ' . $e->getMessage();
        }

        try {
            DB::statement("ALTER TABLE messages ADD COLUMN receiver_type VARCHAR(255) NOT NULL DEFAULT 'user' AFTER receiver_id");
            $results[] = 'Added receiver_type';
        } catch (\Exception $e) {
            $results[] = 'receiver_type: ' . $e->getMessage();
        }

        return response()->json(['results' => $results]);
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


    Route::get('/debug/fk', function () {
        $keys = \Illuminate\Support\Facades\DB::select("
            SELECT CONSTRAINT_NAME, COLUMN_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'messages' 
            AND TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        return $keys;
    });
});
