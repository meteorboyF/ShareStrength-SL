<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\LandingPage;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\RegisterUser;
use App\Livewire\Auth\RegisterHelpMate;
use App\Livewire\Dashboards\UserDashboard;
use App\Livewire\Dashboards\HelpMateDashboard;
use App\Livewire\Dashboards\AdminDashboard;
use App\Http\Controllers\ChatbotController;

Route::get('/', LandingPage::class)->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/helpmate/login', Login::class)->name('helpmate.login');
Route::get('/admin/login', Login::class)->name('admin.login');
Route::get('/register-user', RegisterUser::class)->name('register.user');
Route::get('/register-helpmate', RegisterHelpMate::class)->name('register.helpmate');

// PWD Routes
Route::middleware('auth:pwd')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/post-task', \App\Livewire\Tasks\PostTask::class)->name('tasks.post');
    Route::get('/trusted-contacts', \App\Livewire\TrustedContacts::class)->name('trusted-contacts');
    Route::get('/payment-history', \App\Livewire\PaymentHistory::class)->name('payment-history');
    Route::get('/payment-insights', \App\Livewire\PaymentInsights::class)->name('payment-insights');
    Route::get('/service-payment/{paymentId}', \App\Livewire\ServicePayment::class)->name('service-payment');
});

// HelpMate Routes
Route::middleware('auth:helpmate')->group(function () {
    Route::get('/helpmate-dashboard', HelpMateDashboard::class)->name('helpmate.dashboard');
    Route::get('/browse-tasks', \App\Livewire\Tasks\BrowseTasks::class)->name('tasks.browse');
    Route::get('/task-status', \App\Livewire\Tasks\TaskStatus::class)->name('tasks.status');
});

// Shared Routes (PWD + HelpMate)
Route::middleware('auth:pwd,helpmate')->group(function () {
    Route::get('/resources', \App\Livewire\Resources::class)->name('resources');
    Route::get('/my-profile', \App\Livewire\MyProfile::class)->name('my-profile');
    Route::get('/profile/pwd/{id}', \App\Livewire\ProfileView::class)
        ->defaults('type', 'pwd')
        ->name('profile.view.pwd');
    Route::get('/profile/helpmate/{id}', \App\Livewire\ProfileView::class)
        ->defaults('type', 'helpmate')
        ->name('profile.view.helpmate');
    Route::get('/messages/{conversationId?}', \App\Livewire\Messages::class)->name('messages');

    // Marketplace / Shop
    Route::get('/marketplace', \App\Livewire\Shop\Marketplace::class)->name('marketplace');
    Route::get('/marketplace/product/{id}', \App\Livewire\Shop\ProductDetails::class)->name('product.details');
    Route::get('/cart', \App\Livewire\Shop\Cart::class)->name('cart');
    Route::get('/checkout', \App\Livewire\Shop\Checkout::class)->name('checkout');
});

// Admin Routes
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/resources', \App\Livewire\Admin\ManageResources::class)->name('admin.resources');
});

// Test route
Route::get('/test', \App\Livewire\TestButton::class);

Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
    ->middleware(['throttle:30,1'])
    ->name('chatbot.ask');

Route::get('/chatbot/history', [ChatbotController::class, 'history'])
    ->middleware(['throttle:60,1'])
    ->name('chatbot.history');

Route::post('/chatbot/stream', [ChatbotController::class, 'stream'])
    ->middleware(['throttle:30,1'])
    ->name('chatbot.stream');
