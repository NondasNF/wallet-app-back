<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;

Route::middleware(['auth:sanctum'])->group(function () {
  Route::get('/user', [AuthController::class, 'user']);
  Route::post('/user/logout', [AuthController::class, 'logout']);
  Route::post('/user/logout-all', [AuthController::class, 'logoutAll']);
  Route::get('/user/logged-devices', [AuthController::class, 'loggedDevices']);
  Route::get('/user/wallet', [WalletController::class, 'index']);
  Route::post('/user/transation/deposit', [TransactionController::class, 'deposit']);
  Route::post('/user/transation/transfer', [TransactionController::class, 'transfer']);
  Route::get('/user/transation/history', [TransactionController::class, 'history']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

