<?php

use App\Http\Controllers\ActivityLogController;
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
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // USER MANAGEMENT
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
        Route::middleware('permission:edit_users|edit_assigned_customers')->group(function () {
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
        });
    });

    // MY CUSTOMER
    Route::get('/my-customers', [UserController::class, 'assignedIndex'])
        ->middleware('permission:view_assigned_customers')
        ->name('users.assigned');

    // SYSTEM SETTINGS (Strict Admin Only)
    // Only 'admin' role can touch these, regardless of permissions.
    Route::middleware('role:admin')->prefix('admin')->name('roles.')->group(function () {

        Route::get('/matrix', [RoleManagerController::class, 'index'])->name('matrix');
        Route::post('/matrix', [RoleManagerController::class, 'update'])->name('update');

        // Activity Log Route
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.index');
    });

    // Only Admin & CS accessible
    Route::middleware(['role:cs_staff|cs_leader|admin'])->prefix('office')->name('office.')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\CS\OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\CS\OrderManagementController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/claim', [\App\Http\Controllers\CS\OrderManagementController::class, 'claim'])->name('orders.claim');
        Route::post('/orders/{order}/approve', [\App\Http\Controllers\CS\OrderManagementController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/cancel', [\App\Http\Controllers\CS\OrderManagementController::class, 'cancel'])->name('orders.cancel');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\CS\OrderManagementController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/{order}/handover', [\App\Http\Controllers\CS\OrderManagementController::class, 'handover'])->name('orders.handover');
    });

    // ITEM MANAGEMENT
    Route::resource('items', \App\Http\Controllers\ItemController::class)
        ->middleware(['auth', 'verified']);

    // CATALOG MANAGEMENT
    Route::resource('catalogs', \App\Http\Controllers\CatalogController::class)
        ->middleware(['auth', 'verified']);

    // CUSTOMER PRODUCT VIEWING
    // 1. Product Catalog visibility (whitelist logic) [1]
    Route::get('/products', [\App\Http\Controllers\Customer\ProductCatalogController::class, 'index'])
        ->name('customer.products.index')
        ->middleware('role:customer');

    Route::middleware('role:customer')->group(function () {

        // 2. Reservation / Draft Management (reservation. index, store, etc.) [1, 2]
        Route::prefix('reservation')->name('reservation.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Customer\ReservationController::class, 'index'])->name('index');
            Route::post('/add', [\App\Http\Controllers\Customer\ReservationController::class, 'store'])->name('store');
            Route::put('/{orderItem}', [\App\Http\Controllers\Customer\ReservationController::class, 'update'])->name('update');
            Route::delete('/{orderItem}', [\App\Http\Controllers\Customer\ReservationController::class, 'destroy'])->name('destroy');
            Route::post('/submit', [\App\Http\Controllers\Customer\ReservationController::class, 'submit'])->name('submit');
        });

        // 3. Order History (customer.orders. index, show) [3]
        // Move these OUTSIDE the 'reservation.' name group to fix the naming error
        Route::prefix('my-orders')->name('customer.orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Customer\OrderHistoryController::class, 'index'])->name('index');
            Route::get('/{order}', [\App\Http\Controllers\Customer\OrderHistoryController::class, 'show'])->name('show');
        });
    });

});
