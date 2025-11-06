<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\MembersController;
use App\Http\Controllers\Api\V1\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Version 1 Routes
Route::prefix('v1')->group(function () {

    // Authentication Routes
    Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
        Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
        Route::put('profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
        Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        // Managers: limited capabilities (define BEFORE admin to avoid route shadowing)
        Route::middleware('role:manager')->group(function () {
            // Managers can index and show users, update limited fields, and create employees
            Route::get('users', [MembersController::class, 'index']);
            Route::get('users/{user}', [MembersController::class, 'show']);
            Route::put('users/{user}', [MembersController::class, 'update']);
            Route::post('employees', [MembersController::class, 'storeEmployee']);
        });
        // User Management Routes - Admin full CRUD
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('users', MembersController::class);
            Route::post('users/{user}/assign-role', [MembersController::class, 'assignRole']);
        });

        // Vehicle Management Routes
        Route::get('vehicles/search', [VehicleController::class, 'search']);


        // Task Management Routes
        Route::apiResource('tasks', TaskController::class);
        Route::post('tasks/{task}/assign', [TaskController::class, 'assign']);
        Route::put('tasks/{task}/status', [TaskController::class, 'updateStatus']);
        Route::post('tasks/{task}/attachments', [TaskController::class, 'uploadAttachment']);
        Route::delete('tasks/{task}/attachments/{attachment}', [TaskController::class, 'deleteAttachment']);
        Route::get('tasks/my-tasks', [TaskController::class, 'myTasks']);
        Route::get('tasks/assigned-to-me', [TaskController::class, 'assignedToMe']);

        // Profile Management Routes
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::post('avatar', [ProfileController::class, 'uploadAvatar']);
            Route::delete('avatar', [ProfileController::class, 'removeAvatar']);
            Route::get('task-stats', [ProfileController::class, 'taskStats']);
        });
    });
});
