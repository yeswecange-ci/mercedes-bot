<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\DashboardWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Dashboard Routes (Protected)
Route::middleware('auth')->group(function () {
    // Main Dashboard
    Route::get('/dashboard', [DashboardWebController::class, 'index'])->name('dashboard');

    // Active Conversations
    Route::get('/dashboard/active', [DashboardWebController::class, 'active'])->name('dashboard.active');

    // All Conversations
    Route::get('/dashboard/conversations', [DashboardWebController::class, 'conversations'])->name('dashboard.conversations');

    // Conversation Detail
    Route::get('/dashboard/conversations/{id}', [DashboardWebController::class, 'show'])->name('dashboard.show');

    // Statistics
    Route::get('/dashboard/statistics', [DashboardWebController::class, 'statistics'])->name('dashboard.statistics');

    // Search Free Inputs
    Route::get('/dashboard/search', [DashboardWebController::class, 'search'])->name('dashboard.search');
});
