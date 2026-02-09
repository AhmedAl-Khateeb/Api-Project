<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;

// Login
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Admin routes for managing tasks
    Route::prefix('admin/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->middleware('permission:view all tasks');
        Route::get('/{id}', [TaskController::class, 'show'])->middleware('permission:view one task');
        Route::post('/', [TaskController::class, 'store'])->middleware('permission:create task');
        Route::put('/{id}', [TaskController::class, 'update'])->middleware('permission:update task');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->middleware('permission:delete task');
        Route::patch('/{id}', [TaskController::class, 'changeStatus'])->middleware('permission:change task status');
    });

    // Endpoints for regular users to view and manage their own tasks
    Route::prefix('user/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('/{id}', [TaskController::class, 'show']);
        Route::patch('/{id}', [TaskController::class, 'changeStatus']);
    });
});
