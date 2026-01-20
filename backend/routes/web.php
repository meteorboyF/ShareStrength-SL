<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\LandingPage;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\RegisterUser;
use App\Livewire\Auth\RegisterHelpMate;
use App\Livewire\Dashboards\UserDashboard;
use App\Livewire\Dashboards\HelpMateDashboard;
use App\Livewire\Dashboards\AdminDashboard;

Route::get('/', LandingPage::class)->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/register-user', RegisterUser::class)->name('register.user');
Route::get('/register-helpmate', RegisterHelpMate::class)->name('register.helpmate');

// Dashboards (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/helpmate-dashboard', HelpMateDashboard::class)->name('helpmate.dashboard');
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');

    // Task Management
    Route::get('/post-task', \App\Livewire\Tasks\PostTask::class)->name('tasks.post');
    Route::get('/browse-tasks', \App\Livewire\Tasks\BrowseTasks::class)->name('tasks.browse');

    // User Features
    Route::get('/trusted-contacts', \App\Livewire\TrustedContacts::class)->name('trusted-contacts');
    Route::get('/payment-history', \App\Livewire\PaymentHistory::class)->name('payment-history');
    Route::get('/resources', \App\Livewire\Resources::class)->name('resources');
    Route::get('/my-profile', \App\Livewire\MyProfile::class)->name('my-profile');

    // Admin Features
    Route::get('/admin/resources', \App\Livewire\Admin\ManageResources::class)->name('admin.resources');
});

// Test route
Route::get('/test', \App\Livewire\TestButton::class);
