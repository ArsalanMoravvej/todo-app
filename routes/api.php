<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::get("/todos", [TodoController::class, 'index']);
