<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;

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
});
