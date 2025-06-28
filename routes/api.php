<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/users/login", [UserController::class, "login"])->name("login");

Route::middleware('auth:token')->group(function () {
    Route::get('/users/current', [UserController::class, 'current']);
});
