<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleManagerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Routes (Logged In Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. USER MANAGEMENT
    // We split this because viewing and creating require different permissions.
    Route::prefix('users')->name('users.')->group(function () {

        // List Users (Requires 'view_users' permission)
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:view_users')
            ->name('index');

        // Create New User (Requires 'create_users' permission)
        Route::middleware('permission:create_users')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
        });

        // EDIT USER (Requires 'edit_users' permission)
        Route::middleware('permission:edit_users')->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
        });
    });

    // 3. SYSTEM SETTINGS (Strict Admin Only)
    // Only 'admin' role can touch these, regardless of permissions.
    Route::middleware('role:admin')->prefix('admin')->name('roles.')->group(function () {

        Route::get('/matrix', [RoleManagerController::class, 'index'])->name('matrix');
        Route::post('/matrix', [RoleManagerController::class, 'update'])->name('update');

        // You can add 'Activity Log' routes here later
        // Route::get('/activity', ...);
    });

});
