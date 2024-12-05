<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');




Route::prefix('v1/')->namespace('api/v1/')->group(function () {
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{id}', [TaskController::class, 'show']);
        Route::post('/task-create', [TaskController::class, 'create']);
        Route::post('/task-update', [TaskController::class, 'update']);
        Route::get('/tasks/{id}/delete', [TaskController::class, 'destroy']);
        Route::post('/tasks/filter', [TaskController::class, 'filter']);
        Route::post('/users/role', [UserController::class, 'updateRole']);
    });
});
