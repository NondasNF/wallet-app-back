<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/user', [AuthController::class, 'user']);
  Route::get('/user/wallet', [WalletController::class, 'index']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);
