<?php

use App\Http\Controllers\JurusanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/users/login", [UserController::class, "login"])->name("login");

Route::middleware('auth:token')->group(function () {
    Route::get('/users/current', [UserController::class, 'current']);
    Route::put('/users/update', [UserController::class, 'updatePassword']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    Route::delete('/dba/users/{id}', [UserController::class, 'delete']);
    Route::post('/dba/users', [UserController::class, 'createUser']);
    Route::patch('/dba/users', [UserController::class, 'updateUser']);

    Route::get('/users', [UserController::class, 'search']);

    Route::post("/jurusans", [JurusanController::class, "create"]);
    Route::get("/jurusans/{id}", [JurusanController::class, "get"]);
    Route::delete("/jurusans/{id}", [JurusanController::class, "delete"]);
});
