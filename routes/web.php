<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/logout', [AuthController::class, 'logout']);
Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth.spa');
Route::get('/api/products', [ProductController::class, 'index'])->middleware('auth.spa');
Route::get('/api/users', [UserController::class, 'index'])->middleware('auth.spa');
Route::get('/api/users/{id}', [UserController::class, 'show'])->middleware('auth.spa');
Route::post('/api/users', [UserController::class, 'store'])->middleware('auth.spa');
Route::put('/api/users/{id}', [UserController::class, 'update'])->middleware('auth.spa');
Route::delete('/api/users/{id}', [UserController::class, 'destroy'])->middleware('auth.spa');

Route::view('/{any}', 'app')->where('any', '.*');
