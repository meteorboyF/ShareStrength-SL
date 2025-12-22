<?php

use Illuminate\Support\Facades\Route;
use App\Models\Task;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HelperDashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-helpmate', [AuthController::class, 'registerHelper']);
Route::get('/helpmate-dashboard/{id}', [HelperDashboardController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);


// Test route to see if the door is open
Route::get('/test', function () {
    return response()->json(['message' => 'API door is OPEN!']);
});

// The actual data route
Route::get('/tasks', function () {
    return Task::all();
});

