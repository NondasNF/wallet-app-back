<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use GuzzleHttp\Middleware;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/user', [AuthController::class, 'user']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);
