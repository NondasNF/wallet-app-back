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
  Route::put('/user/wallet', [WalletController::class, 'changeStatus']);
  Route::post('/user/transaction/deposit', [TransactionController::class, 'deposit']);
  Route::post('/user/transaction/transfer', [TransactionController::class, 'transfer']);
  Route::get('/user/transaction/history', [TransactionController::class, 'history']);
  Route::put('/user/transaction/cancel/{id}', [TransactionController::class, 'cancel']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

