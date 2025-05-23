<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TodoController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/todos', [TodoController::class, 'index']);
        Route::post('/todos', [TodoController::class, 'store']);
        Route::get('/todos/{todo}', [TodoController::class, 'show'])->can('view', 'todo');
        Route::put('/todos/{todo}', [TodoController::class, 'update'])->can('update', 'todo');
        Route::patch('/todos/{todo}', [TodoController::class, 'update'])->can('update', 'todo');
        Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->can('delete', 'todo');
    });
});
