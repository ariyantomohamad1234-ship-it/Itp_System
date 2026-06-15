<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ItpApiController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\MessageApiController;
use App\Http\Controllers\Api\ProjectApiController;

// Public
Route::post('/login', [AuthApiController::class, 'login']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);
    
    // My Projects Skeletal API
    Route::get('/my-projects', [ProjectApiController::class, 'index']);

    // Navigation drill-down
    Route::get('/projects', [ItpApiController::class, 'projects']);
    Route::get('/projects/{id}/moduls', [ItpApiController::class, 'moduls']);
    Route::get('/moduls/{id}/bloks', [ItpApiController::class, 'bloks']);
    Route::get('/bloks/{id}/subbloks', [ItpApiController::class, 'subbloks']);
    Route::get('/subbloks/{id}/assembly', [ItpApiController::class, 'assembly']);

    // ITP Data
    Route::get('/itp-data/{id}', [ItpApiController::class, 'showItpData']);
    Route::post('/itp-data', [ItpApiController::class, 'storeItpData']);
    Route::post('/itp-data/{id}/approve', [ItpApiController::class, 'approveItpData']);
    Route::post('/itp-data/{id}/reject', [ItpApiController::class, 'rejectItpData']);

    // Messages & Notifications
    Route::get('/messages/channels', [MessageApiController::class, 'channels']);
    Route::get('/messages/{projectId}', [MessageApiController::class, 'messages']);
    Route::post('/messages/send', [MessageApiController::class, 'send']);
    Route::get('/notifications', [MessageApiController::class, 'notifications']);
    Route::get('/notifications/unread-count', [MessageApiController::class, 'unreadNotificationCount']);
    Route::post('/notifications/{id}/mark-read', [MessageApiController::class, 'markNotificationRead']);
    Route::post('/notifications/mark-all-read', [MessageApiController::class, 'markAllNotificationsRead']);

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminApiController::class, 'dashboard']);
        Route::get('/users', [AdminApiController::class, 'users']);
        Route::post('/users', [AdminApiController::class, 'storeUser']);
        Route::delete('/users/{id}', [AdminApiController::class, 'deleteUser']);
        Route::post('/projects', [AdminApiController::class, 'storeProject']);
        Route::post('/projects/{id}/toggle-status', [AdminApiController::class, 'toggleProjectStatus']);
        Route::post('/projects/assign-user', [AdminApiController::class, 'assignUser']);
        Route::delete('/projects/{project}/users/{user}', [AdminApiController::class, 'unassignUser']);
        Route::get('/templates', [AdminApiController::class, 'templates']);
    });
});
