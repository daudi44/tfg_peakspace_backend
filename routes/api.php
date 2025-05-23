<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\TimeEntriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovementsController;
use App\Http\Controllers\SubscriptionsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/add-category', [CategoriesController::class, 'addCategory']);
    Route::post('/delete-category', [CategoriesController::class, 'deleteCategory']);
    Route::get('/economy-categories', [CategoriesController::class, 'getEconomyCategories']);
    Route::get('/productivity-categories', [CategoriesController::class, 'getProductivityCategories']);

    Route::post('/add-task', [TasksController::class, 'addTask']);
    Route::post('/update-task', [TasksController::class, 'updateTask']);
    Route::post('/delete-task', [TasksController::class, 'deleteTask']);
    Route::get('/all-tasks', [TasksController::class, 'getAllTasks']);
    Route::post('/tasks', [TasksController::class, 'getTasksByStatus']);
    Route::post('/update-task-status', [TasksController::class, 'updateTaskStatus']);

    Route::post('/start-time-entry', [TimeEntriesController::class, 'startTimeEntry']);
    Route::post('/stop-time-entry', [TimeEntriesController::class, 'stopTimeEntry']);
    Route::get('/time-entries', [TimeEntriesController::class, 'getTimeEntries']);
    Route::get('/last-time-entry', [TimeEntriesController::class, 'getLastTimeEntry']);
    Route::post('/total-time', [TimeEntriesController::class, 'getTotalTime']);
    Route::post('/delete-time-entry', [TimeEntriesController::class, 'deleteTimeEntry']);

    Route::post('/update-user', [UserController::class, 'updateProfile']);
    Route::post('/delete-account', [UserController::class, 'deleteAccount']);
    Route::post('/set-balance', [UserController::class, 'setBalance']);
    Route::get('/get-balance', [UserController::class, 'getBalance']);

    Route::post('/add-movement', [MovementsController::class, 'addMovement']);
    Route::post('/edit-movement', [MovementsController::class, 'editMovement']);
    Route::post('/delete-movement', [MovementsController::class, 'deleteMovement']);
    Route::post('/movements', [MovementsController::class, 'getMovementsByType']);
    Route::post('/last-month-movements', [MovementsController::class, 'getLast30DaysMovements']);

    Route::post('/add-subscription', [SubscriptionsController::class, 'addSubscription']);
    Route::post('/edit-subscription', [SubscriptionsController::class, 'editSubscription']);
    Route::post('/delete-subscription', [SubscriptionsController::class, 'deleteSubscription']);
    Route::get('/subscriptions', [SubscriptionsController::class, 'getSubscriptions']);
});
