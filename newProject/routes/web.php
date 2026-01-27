<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\LandingPage;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\RegisterAdmin;
use App\Livewire\Auth\RegisterProvider;
use App\Livewire\Auth\RegisterUser;
use App\Livewire\Dashboards\AdminDashboard;
use App\Livewire\Dashboards\ProviderDashboard;
use App\Livewire\Dashboards\UserDashboard;
use App\Livewire\Profiles\ProviderProfile;
use App\Livewire\Profiles\ProviderPublicProfile;
use App\Livewire\Profiles\UserProfile;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AccessibilitySettingsController;
use App\Http\Controllers\ChatbotController;

Route::get('/', LandingPage::class)->name('home');

Route::get('/login', Login::class)->middleware('guest')->name('login');
Route::get('/provider/login', Login::class)->middleware('guest:provider')->name('provider.login');
Route::get('/admin/login', Login::class)->middleware('guest:admin')->name('admin.login');

Route::get('/register-user', RegisterUser::class)->middleware('guest')->name('register.user');
Route::get('/register-provider', RegisterProvider::class)->middleware('guest:provider')->name('register.provider');
Route::get('/register-admin', RegisterAdmin::class)->middleware('guest:admin')->name('register.admin');

Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/profile', UserProfile::class)->name('profile.user');
});

Route::middleware('auth:provider')->group(function () {
    Route::get('/provider/dashboard', ProviderDashboard::class)->name('provider.dashboard');
    Route::get('/provider/profile', ProviderProfile::class)->name('profile.provider');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
});

Route::post('/logout', LogoutController::class)->name('logout');

Route::get('/providers/{providerId}', ProviderPublicProfile::class)->name('providers.show');

Route::get('/accessibility/settings', [AccessibilitySettingsController::class, 'show'])
    ->name('accessibility.settings.show');

Route::post('/accessibility/settings', [AccessibilitySettingsController::class, 'update'])
    ->name('accessibility.settings.update');

Route::post('/chatbot/ask', [ChatbotController::class, 'ask'])
    ->middleware(['throttle:30,1'])
    ->name('chatbot.ask');

Route::get('/chatbot/history', [ChatbotController::class, 'history'])
    ->middleware(['throttle:60,1'])
    ->name('chatbot.history');

Route::post('/chatbot/stream', [ChatbotController::class, 'stream'])
    ->middleware(['throttle:30,1'])
    ->name('chatbot.stream');
