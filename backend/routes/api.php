<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrustedContactController;
use App\Http\Controllers\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Donation Routes
    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/financial-transparency', [DonationController::class, 'index']);

    // Shop Routes (Public)
    Route::apiResource('products', \App\Http\Controllers\ProductController::class)->only(['index', 'show']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);

        // Feature Routes
        Route::get('my-tasks', [TaskController::class, 'myTasks']);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('trusted-contacts', TrustedContactController::class)->only(['index', 'store']);
        Route::apiResource('messages', MessageController::class)->except(['update']);
        Route::patch('messages/{id}/read', [MessageController::class, 'markAsRead']);

        Route::apiResource('conversations', ConversationController::class)->only(['index', 'show']);
        Route::post('conversations/get-or-create', [ConversationController::class, 'getOrCreate']);
        Route::patch('conversations/{id}/read', [MessageController::class, 'markConversationAsRead']);

        Route::get('/resources', [\App\Http\Controllers\ResourceController::class, 'index']);
        Route::get('/resources/featured', [\App\Http\Controllers\ResourceController::class, 'featured']);
        Route::get('/resources/search', [\App\Http\Controllers\ResourceController::class, 'search']);
        Route::get('/resources/categories', [\App\Http\Controllers\ResourceCategoryController::class, 'index']);
        Route::get('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'show']);
        Route::get('/resources/{id}/download', [\App\Http\Controllers\ResourceController::class, 'download']);

        Route::apiResource('communities', CommunityController::class);
        Route::apiResource('resources', \App\Http\Controllers\ResourceController::class)->except(['index', 'show']);
        Route::apiResource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'store', 'update']);
        Route::get('/payments/summary', [\App\Http\Controllers\PaymentController::class, 'summary']);
        Route::get('/payments/insights', [\App\Http\Controllers\PaymentController::class, 'insights']);
        Route::get('/applications/received', [\App\Http\Controllers\ApplicationController::class, 'received']);
        Route::apiResource('applications', \App\Http\Controllers\ApplicationController::class)->only(['index', 'store', 'update']);
        Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store']);

        // User Profile Routes
        Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
        Route::put('/profile', [\App\Http\Controllers\UserController::class, 'update']);
        Route::post('/profile/photo', [\App\Http\Controllers\UserController::class, 'uploadPhoto']);

        // HelpMate Profile Routes
        Route::get('/helpers', [HelperController::class, 'index']);
        Route::get('/helpers/{id}', [HelperController::class, 'show']);
        Route::put('/helper/profile', [HelperController::class, 'update']);
        Route::post('/helper/profile/photo', [HelperController::class, 'uploadPhoto']);

        // Resource Routes (Public)
        Route::get('/resources', [\App\Http\Controllers\ResourceController::class, 'index']);
        Route::get('/resources/featured', [\App\Http\Controllers\ResourceController::class, 'featured']);
        Route::get('/resources/search', [\App\Http\Controllers\ResourceController::class, 'search']);
        Route::get('/resources/categories', [\App\Http\Controllers\ResourceCategoryController::class, 'index']);
        Route::get('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'show']);
        Route::get('/resources/{id}/download', [\App\Http\Controllers\ResourceController::class, 'download']);

        // Admin-only resource management
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
            // Admin Dashboard Stats
            Route::get('/admin/stats', [\App\Http\Controllers\AdminController::class, 'getStats']);
            
            // User Management
            Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'getUserList']);
            Route::put('/admin/users/{id}/verify', [\App\Http\Controllers\AdminController::class, 'verifyUser']);
            Route::put('/admin/users/{id}/suspend', [\App\Http\Controllers\AdminController::class, 'suspendUser']);
            
            // Helper Management
            Route::get('/admin/helpers', [\App\Http\Controllers\AdminController::class, 'getHelperList']);
            Route::put('/admin/helpers/{id}/verify', [\App\Http\Controllers\AdminController::class, 'verifyHelper']);
            Route::put('/admin/helpers/{id}/suspend', [\App\Http\Controllers\AdminController::class, 'suspendHelper']);
            
            // Payment Management
            Route::get('/admin/payments', [\App\Http\Controllers\AdminController::class, 'getPaymentList']);
            Route::put('/admin/payments/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approvePayment']);
            
            // Review Management
            Route::get('/admin/reviews', [\App\Http\Controllers\AdminController::class, 'getReviewList']);
            Route::delete('/admin/reviews/{id}', [\App\Http\Controllers\AdminController::class, 'deleteReview']);
            
            // Resource Management
            Route::post('/admin/resources/upload', [\App\Http\Controllers\ResourceController::class, 'upload']);
            Route::delete('/admin/resources/{id}/delete', [\App\Http\Controllers\ResourceController::class, 'delete']);
            
            Route::post('/resources', [\App\Http\Controllers\ResourceController::class, 'store']);
            Route::put('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'update']);
            Route::delete('/resources/{id}', [\App\Http\Controllers\ResourceController::class, 'destroy']);

            // Category management
            Route::post('/categories', [\App\Http\Controllers\ResourceCategoryController::class, 'store']);
            Route::put('/categories/{id}', [\App\Http\Controllers\ResourceCategoryController::class, 'update']);
            Route::delete('/categories/{id}', [\App\Http\Controllers\ResourceCategoryController::class, 'destroy']);
        });

        Route::post('orders', [\App\Http\Controllers\OrderController::class, 'store']);

        // Matching
        Route::put('/tasks/{id}/accept', [TaskController::class, 'accept']);
        Route::put('/tasks/{id}/start', [TaskController::class, 'start']);
        Route::put('/tasks/{id}/pause', [TaskController::class, 'pause']);
        Route::put('/tasks/{id}/resume', [TaskController::class, 'resume']);
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
                'messages_columns' => Schema::hasTable('messages') ? Schema::getColumnListing('messages') : [],
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
            if (!Schema::hasTable('conversations')) {
                Schema::create('conversations', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('user_one_id');
                    $table->string('user_one_type')->default('user');
                    $table->unsignedBigInteger('user_two_id');
                    $table->string('user_two_type')->default('user');
                    $table->unsignedBigInteger('task_id')->nullable();
                    $table->timestamp('last_message_at')->nullable();
                    $table->timestamps();

                    $table->index(['user_one_id', 'user_one_type'], 'idx_user_one');
                    $table->index(['user_two_id', 'user_two_type'], 'idx_user_two');
                });
                $results[] = 'Conversations table created';
            } else {
                $results[] = 'Conversations table already exists';
            }
        } catch (\Exception $e) {
            $results[] = 'Conversations: ' . $e->getMessage();
        }

        try {
            if (!Schema::hasTable('messages')) {
                Schema::create('messages', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('conversation_id')->nullable();
                    $table->unsignedBigInteger('task_id')->nullable();
                    $table->unsignedBigInteger('sender_id');
                    $table->string('sender_type')->default('user');
                    $table->unsignedBigInteger('receiver_id');
                    $table->string('receiver_type')->default('user');
                    $table->text('content');
                    $table->boolean('is_read')->default(false);
                    $table->timestamps();

                    $table->index('conversation_id');
                    $table->index('sender_id');
                    $table->index('receiver_id');
                });
                $results[] = 'Messages table created';
            } else {
                Schema::table('messages', function ($table) {
                    if (!Schema::hasColumn('messages', 'conversation_id')) {
                        $table->unsignedBigInteger('conversation_id')->nullable()->index();
                    }
                    if (!Schema::hasColumn('messages', 'sender_type')) {
                        $table->string('sender_type')->default('user');
                    }
                    if (!Schema::hasColumn('messages', 'receiver_type')) {
                        $table->string('receiver_type')->default('user');
                    }
                    if (!Schema::hasColumn('messages', 'is_read')) {
                        $table->boolean('is_read')->default(false);
                    }
                    if (!Schema::hasColumn('messages', 'task_id')) {
                        $table->unsignedBigInteger('task_id')->nullable();
                    }
                });
                $results[] = 'Messages table updated';
            }
        } catch (\Exception $e) {
            $results[] = 'Messages: ' . $e->getMessage();
        }

        return response()->json(['results' => $results]);
    });

    Route::get('/fix-db', function () {
        $results = [];
        try {
            // Fix 1: Change status column to VARCHAR
            if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'status')) {
                Schema::table('tasks', function ($table) {
                    $table->string('status')->default('open')->change();
                });
                $results[] = 'Status column updated to VARCHAR(50)';
            } else {
                $results[] = 'Status column not found';
            }
        } catch (\Exception $e) {
            $results[] = 'Status column error: ' . $e->getMessage();
        }

        try {
            // Fix 2: Add started_at column if not exists
            if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'started_at')) {
                Schema::table('tasks', function ($table) {
                    $table->timestamp('started_at')->nullable();
                });
                $results[] = 'started_at column added';
            } else {
                $results[] = 'started_at column already exists';
            }
        } catch (\Exception $e) {
            $results[] = 'started_at: ' . $e->getMessage();
        }

        try {
            // Fix 3: Add completed_at column if not exists
            if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'completed_at')) {
                Schema::table('tasks', function ($table) {
                    $table->timestamp('completed_at')->nullable();
                });
                $results[] = 'completed_at column added';
            } else {
                $results[] = 'completed_at column already exists';
            }
        } catch (\Exception $e) {
            $results[] = 'completed_at: ' . $e->getMessage();
        }

        return response()->json(['results' => $results]);
    });


    Route::get('/debug/fk', function () {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return DB::select("PRAGMA foreign_key_list('messages')");
        }

        if ($driver === 'mysql') {
            return DB::select("
                SELECT CONSTRAINT_NAME, COLUMN_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'messages' 
                AND TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
        }

        return [];
    });
});
