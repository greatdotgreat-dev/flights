<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminAgentsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminBookingsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Agent\AgentAuthController;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Charge\ChargeController; 
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AgentLoginController;
use App\Http\Controllers\Auth\ChargeLoginController;
use App\Http\Controllers\Charge\ChargingDashboardController; 
use App\Http\Controllers\Charge\ChargingController;
use App\Http\Controllers\AuthConsentController;
use App\Http\Controllers\Admin\AdminNotifyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;


// charge booking controller 
use App\Http\Controllers\ChargeBookingController;
/*
|--------------------------------------------------------------------------
| Web Routes - Laravel 11 Compatible
|--------------------------------------------------------------------------
*/

// email consent routes 
        // Customer access route (Signed for security)
Route::get('/consent/{id}', [AuthConsentController::class, 'customerConsentView'])
    ->name('customer.consent.view')
    ->middleware('signed'); // This prevents tampering with the ID



// agent auth routes 
Route::get('/', [AgentLoginController::class, 'showLoginForm'])->name('agent.login');
Route::get('/agent/login', [AgentLoginController::class, 'showLoginForm'])->name('agent.login');
Route::post('/agent/login', [AgentLoginController::class, 'login']);
Route::post('/agent/logout', [AgentLoginController::class, 'logout'])->name('agent.logout');

// admin auth routes 
Route::get('/Admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/Admin/login', [AdminLoginController::class, 'login']);
Route::post('/agent/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// charge auth routes
Route::get('/charge/login', [ChargeLoginController::class, 'showLoginForm'])->name('charge.login');
Route::post('/charge/login', [ChargeLoginController::class, 'login']);
Route::post('/charge/logout', [ChargeLoginController::class, 'logout'])->name('charge.logout');
// web.php

// CHARGING TEAM
Route::middleware(['auth', 'role:charge'])->prefix('charge')->name('charge.')->group(function () {
    Route::get('/dashboard', [ChargingDashboardController::class, 'index'])->name('dashboard');
    Route::get('/assignments/{assignment}/details', [ChargeController::class, 'showDetails'])->name('assignments.details');
    // Route::get('/bookings/show/{booking}', [ChargeController::class, 'show'])->name('bookings.show');
    Route::post('/assignments/{assignment}/accept', [ChargeController::class, 'accept'])->name('assignments.accept');
    Route::post('/assignments/{assignment}/reject', [ChargeController::class, 'reject'])->name('assignments.reject');
    Route::get('/bookings/{booking}', [BookingController::class, 'chargeShow'])->name('bookings.show');

        // Page to view and edit the authorization email
    Route::get('/booking/{id}/authorize-edit', [AuthConsentController::class, 'edit'])
        ->name('authorize.edit');
    
    // Preview page after editing
    Route::post('/booking/{id}/authorize-preview', [AuthConsentController::class, 'preview'])
        ->name('authorize.preview');
    
    // Final action to send the email
    Route::post('/booking/{id}/authorize-send', [AuthConsentController::class, 'send'])
        ->name('authorize.send');


});


// AGENT DASHBOARD ROUTES ONLY (POST-LOGIN)
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    // Route::get('/dashboard', [BookingController::class, 'agentIndex'])->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'Index'])->name('dashboard'); 

    // Route::get('/dashboard', [BookingController::class, 'agentIndex'])->name('dashboard'); 
    Route::get('/bookings', [BookingController::class, 'agentIndex'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'agentCreate'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'agentStore'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'agentShow'])->name('bookings.show');
    Route::get('//{booking}/edit', [BookingController::class, 'agentEdit'])->name('bookings.edit');
    Route::get('/bookings/{booking}/charge', [BookingController::class, 'chargeByAgent'])->name('bookings.charge');
    Route::post('/bookings/{booking}/charge/assign', [BookingController::class, 'assignForCharging'])->name('bookings.charge.assign');
});

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin|manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/agents-list', [\App\Http\Controllers\Admin\AdminAgentsController::class, 'index'])->name('agents.index');
    Route::get('/bookings/all', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'destroy'])->name('bookings.destroy');
    // Settings...
});



// MIS PANEL ROUTES - Role: mis
Route::middleware(['auth', 'role:mis'])->prefix('mis')->name('mis.')->group(function () {
    Route::get('/bookings', [AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}/edit', [AdminBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [AdminBookingsController::class, 'update'])->name('bookings.update');
});



// GENERAL LOGOUT
Route::middleware('auth')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});


// Admin Authentication Routes (No middleware needed here)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Protected Admin Routes (Both Admin and Manager can access)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|manager'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');
    
    // admin can change the status of the agent by clicking on the button in the agents list page, 
    Route::post('/agents/{agent}/toggle-status', [AdminAgentsController::class, 'toggleStatus'])
    ->name('admin.agents.toggleStatus');
    Route::post('/agents/{agent}/toggle-status', [AdminAgentsController::class, 'toggleStatus'])->name('agents.toggleStatus');
    
// User Management Routes (Only Admin can access these)
    Route::prefix('users')->name('users.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{id}/toggle-block', [UserController::class, 'toggleBlock'])->name('toggle-block');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
     // Notification Management Routes (Only Admin)
    Route::prefix('notifications')->name('notifications.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [AdminNotifyController::class, 'index'])->name('index');
        Route::get('/create', [AdminNotifyController::class, 'create'])->name('create');
        Route::post('/', [AdminNotifyController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminNotifyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminNotifyController::class, 'update'])->name('update');
        Route::post('/{id}/duplicate', [AdminNotifyController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{id}/toggle-active', [AdminNotifyController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{id}', [AdminNotifyController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/stats', [AdminNotifyController::class, 'stats'])->name('stats');
    });
    // Settings (Both Admin and Manager can access)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Reports (Both Admin and Manager can access)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    // Agent Management (Both Admin and Manager can access)
    Route::get('/agents', [AdminAgentsController::class, 'index'])->name('agents.index');
    Route::get('/agents/{agent}', [AdminAgentsController::class, 'show'])->name('agents.show');
    
    // Bookings (Both Admin and Manager can access)
    Route::get('/bookings', [AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingsController::class, 'show'])->name('bookings.show');
    

    });



    Route::middleware(['auth'])->group(function () {
    // Notification routes for all authenticated users
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});

Route::get('/agent/test-notifications', function() {
    return view('agent.test-notifications');
})->middleware(['auth', 'role:agent'])->name('agent.test');
