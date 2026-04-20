<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AdminAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Redirect root → login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AdminAuthController::class, 'login'])
    ->name('login.submit');

Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [AdminAuthController::class, 'register'])
    ->name('register.submit');

Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::post('/forgot-password', [AdminAuthController::class, 'sendPasswordResetLink'])
    ->name('password.email');

Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])
    ->name('password.reset');

Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
    ->name('password.update');

// Logout (GLOBAL - important fix)
Route::post('/logout', [AdminAuthController::class, 'logout'])
    ->name('logout');

Route::middleware(['custom.auth'])->group(function () {
    Route::get('/profile', [AdminAuthController::class, 'showProfile'])
        ->name('profile.show');
    Route::put('/profile', [AdminAuthController::class, 'updateProfile'])
        ->name('profile.update');

    Route::get('/notifications/recent', [NotificationController::class, 'recent'])
        ->name('notifications.recent');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])
        ->name('notifications.read-all');
    Route::delete('/notifications/{requestUpdate}/dismiss', [NotificationController::class, 'dismiss'])
        ->name('notifications.dismiss');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['custom.auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/profile', [AdminAuthController::class, 'showProfile'])
            ->name('admin.profile.show');

        Route::put('/profile', [AdminAuthController::class, 'updateProfile'])
            ->name('admin.profile.update');

        Route::get('/manage-users', [AdminAuthController::class, 'manageUsers'])
            ->name('admin.manage-users');

        Route::get('/manage-users/create', [AdminAuthController::class, 'createStaffUser'])
            ->name('admin.users.create');

        Route::post('/manage-users', [AdminAuthController::class, 'storeStaffUser'])
            ->name('admin.users.store');

        Route::patch('/manage-users/{user}/role', [AdminAuthController::class, 'updateUserRole'])
            ->name('admin.users.role');

        Route::post('/categories', [AdminAuthController::class, 'storeCategory'])
            ->name('admin.categories.store');
        Route::get('/categories', [AdminAuthController::class, 'categories'])
            ->name('admin.categories.index');
        Route::patch('/categories/{category}', [AdminAuthController::class, 'updateCategory'])
            ->name('admin.categories.update');
        Route::delete('/categories/{category}', [AdminAuthController::class, 'destroyCategory'])
            ->name('admin.categories.destroy');

        // Admin Requests
        Route::get('/requests', [ServiceRequestController::class, 'allRequests'])
            ->name('admin.requests');

        Route::get('/requests/{serviceRequest}', [ServiceRequestController::class, 'show'])
            ->name('admin.requests.show');

        Route::post('/requests/{id}/assign', [ServiceRequestController::class, 'assign'])
            ->name('admin.requests.assign');

        Route::patch('/requests/{id}/status', [ServiceRequestController::class, 'updateStatus'])
            ->name('admin.requests.status');
    });


/*
|--------------------------------------------------------------------------
| Service Staff Routes
|--------------------------------------------------------------------------
*/

Route::prefix('staff')
    ->middleware(['custom.auth', 'role:service_staff'])
    ->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])
            ->name('staff.dashboard');

        Route::get('/requests', [ServiceRequestController::class, 'assignedRequests'])
            ->name('staff.requests');

        Route::get('/requests/{serviceRequest}', [ServiceRequestController::class, 'show'])
            ->name('staff.requests.show');

        Route::patch('/requests/{id}/status', [ServiceRequestController::class, 'updateStatus'])
            ->name('staff.requests.status');
    });


/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['custom.auth', 'role:client'])->group(function () {

    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('user.dashboard');

    Route::get('/requests', [ServiceRequestController::class, 'index'])
        ->name('requests.index');

    Route::get('/requests/create', [ServiceRequestController::class, 'create'])
        ->name('requests.create');

    Route::get('/requests/{serviceRequest}', [ServiceRequestController::class, 'show'])
        ->name('requests.show');

    Route::post('/requests', [ServiceRequestController::class, 'store'])
        ->name('requests.store');
});
