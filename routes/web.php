<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/logout', [AuthController::class, 'logout']);
Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth.spa');
Route::put('/api/profile', [ProfileController::class, 'update'])->middleware('auth.spa');
Route::get('/api/products', [ProductController::class, 'index'])->middleware('auth.spa');
Route::get('/api/products/{id}', [ProductController::class, 'show'])->middleware('auth.spa');
Route::post('/api/products', [ProductController::class, 'store'])->middleware('auth.spa');
Route::put('/api/products/{id}', [ProductController::class, 'update'])->middleware('auth.spa');
Route::delete('/api/products/{id}', [ProductController::class, 'destroy'])->middleware('auth.spa');
Route::get('/api/users', [UserController::class, 'index'])->middleware('auth.spa');
Route::get('/api/users/{id}', [UserController::class, 'show'])->middleware('auth.spa');
Route::post('/api/users', [UserController::class, 'store'])->middleware('auth.spa');
Route::put('/api/users/{id}', [UserController::class, 'update'])->middleware('auth.spa');
Route::delete('/api/users/{id}', [UserController::class, 'destroy'])->middleware('auth.spa');
Route::get('/api/accounts', [AccountController::class, 'index'])->middleware('auth.spa');
Route::get('/api/accounts/{id}', [AccountController::class, 'show'])->middleware('auth.spa');
Route::post('/api/accounts', [AccountController::class, 'store'])->middleware('auth.spa');
Route::put('/api/accounts/{id}', [AccountController::class, 'update'])->middleware('auth.spa');
Route::delete('/api/accounts/{id}', [AccountController::class, 'destroy'])->middleware('auth.spa');

Route::view('/{any}', 'app')->where('any', '.*');
