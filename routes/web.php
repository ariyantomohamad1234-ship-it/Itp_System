<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ItpController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [AuthController::class, 'loginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    // === NOTIFICATIONS ===
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // === ADMIN ===
    Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users/create', [AdminController::class, 'createUser']);
        Route::post('/users', [AdminController::class, 'storeUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/projects/create', [AdminController::class, 'createProject']);
        Route::post('/projects', [AdminController::class, 'storeProject']);
        Route::post('/projects/{id}/toggle-status', [AdminController::class, 'toggleProjectStatus']);
        Route::post('/projects/assign-user', [AdminController::class, 'assignUser']);
        Route::delete('/projects/{project}/users/{user}', [AdminController::class, 'unassignUser']);
        Route::get('/projects/{id}/manage', [AdminController::class, 'manageProject']);
        Route::post('/moduls', [AdminController::class, 'storeModul']);
        Route::post('/bloks', [AdminController::class, 'storeBlok']);
        Route::post('/sub-bloks', [AdminController::class, 'storeSubBlok']);
        Route::post('/itps', [AdminController::class, 'storeItp']);
        Route::delete('/moduls/{id}', [AdminController::class, 'deleteModul']);
        Route::delete('/bloks/{id}', [AdminController::class, 'deleteBlok']);
        Route::delete('/sub-bloks/{id}', [AdminController::class, 'deleteSubBlok']);
        Route::delete('/itps/{id}', [AdminController::class, 'deleteItp']);
        Route::post('/moduls/{id}/schedule', [AdminController::class, 'updateModulSchedule']);
    });

    // === NON-ADMIN ===
    Route::get('/dashboard', [ItpController::class, 'dashboard']);
    Route::get('/modul/{project}', [ItpController::class, 'modul']);
    Route::get('/blok/{modul}', [ItpController::class, 'blok']);
    Route::get('/subblok/{blok}', [ItpController::class, 'subblok']);
    Route::get('/assembly/{subblok}', [ItpController::class, 'assembly']);

    // ITP Data (AJAX)
    Route::get('/itp-data/{itp}', [ItpController::class, 'showItpData']);
    Route::post('/itp-data', [ItpController::class, 'storeItpData']);
    Route::post('/itp-data/{id}/approve', [ItpController::class, 'approveItpData']);
    Route::post('/itp-data/{id}/reject', [ItpController::class, 'rejectItpData']);

    // === MESSAGES ===
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages/send', [MessageController::class, 'send']);
    Route::get('/messages/fetch', [MessageController::class, 'fetch']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
});