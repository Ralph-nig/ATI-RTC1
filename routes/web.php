<?php

use App\Http\Controllers\Client\UserController;
use App\Http\Controllers\Client\SuppliesController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\ReportController;
use App\Http\Controllers\Client\StockCardController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\HelpController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Root route - redirect to dashboard after login
Route::get('/', function () {
    return auth()->check() ? redirect('/client/dashboard') : redirect('/home');
});

// Authentication routes
Auth::routes();

// Home route (landing page before login)
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Protected client routes
Route::prefix('client')->middleware('auth:web')->group(function(){
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('client.dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('client.dashboard.stats');
    Route::get('/dashboard/recent-items', [DashboardController::class, 'getRecentItems'])->name('client.dashboard.recent');
    Route::get('/dashboard/low-stock', [DashboardController::class, 'getLowStockItems'])->name('client.dashboard.lowstock');
    
    // User management routes
    Route::resource('users', UserController::class);
    
    // Profile settings routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('client.profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('client.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('client.profile.password');
    Route::delete('/profile/remove-avatar', [ProfileController::class, 'removeAvatar'])->name('client.profile.remove-avatar');
    
    // Supplies routes with additional functionality
    Route::resource('supplies', SuppliesController::class);
    
    // Additional supplies routes
    Route::get('supplies-export', [SuppliesController::class, 'export'])->name('supplies.export');
    
    // Report routes
    Route::resource('reports', ReportController::class)->names([
        'index' => 'client.reports.index'
    ]);
        
    // Individual report routes
    Route::get('report/rsmi', [ReportController::class, 'rsmi'])->name('client.report.rsmi');
    Route::get('report/rpci', [ReportController::class, 'rpci'])->name('client.report.rpci');
    Route::get('report/ppes', [ReportController::class, 'ppes'])->name('client.report.ppes');
    Route::get('report/rpc-ppe', [ReportController::class, 'rpcPpe'])->name('client.report.rpc-ppe');

    // Stock Card routes
    Route::resource('stockcard', StockCardController::class)->names([
        'index' => 'client.stockcard.index'
    ]);
    
    // Help routes 
    Route::resource('help', HelpController::class)->names([
        'index' => 'client.help.index',
        'create' => 'client.help.create',
        'store' => 'client.help.store',
        'show' => 'client.help.show',
        'edit' => 'client.help.edit',
        'update' => 'client.help.update',
        'destroy' => 'client.help.destroy'
    ]);
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent']);

    // Alternative route names for backward compatibility
    Route::get('inventory', [SuppliesController::class, 'index'])->name('client.inventory');
    Route::get('inventory/{supply}', [SuppliesController::class, 'show'])->name('client.inventory.show');
});

// Fallback route for authenticated users
Route::middleware('auth:web')->group(function(){
    // Redirect authenticated users to dashboard if they hit /home
    Route::get('/home', function() {
        return redirect('/client/dashboard');
    });
});