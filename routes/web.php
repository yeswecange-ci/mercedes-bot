<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\DashboardWebController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\ClientController;
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

    // Chat Routes (Agent Communication)
    Route::prefix('dashboard/chat')->name('dashboard.chat.')->group(function () {
        Route::get('/{id}', [ChatController::class, 'show'])->name('show');
        Route::post('/{id}/take-over', [ChatController::class, 'takeOver'])->name('take-over');
        Route::post('/{id}/send', [ChatController::class, 'send'])->name('send');
        Route::post('/{id}/close', [ChatController::class, 'close'])->name('close');
    });

    // Clients Routes
    Route::prefix('dashboard/clients')->name('dashboard.clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/sync', [ClientController::class, 'sync'])->name('sync');
        Route::get('/{id}', [ClientController::class, 'show'])->name('show');
    });
});
