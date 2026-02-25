<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\authController;
use \App\Http\Controllers\ProductController;
use \App\Http\Controllers\CartController;
use \App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Cache;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::post('/register', [authController::class, 'register']);
Route::post('/login', [authController::class, 'login']);
Route::middleware(['auth:sanctum'])->post('/logout', [authController::class, 'logout']);
Route::post('/forgetPass', [authController::class, 'forgetPass']);
Route::PUT('/resetPass', [authController::class, 'resetPass']);

// Product routes
Route::resource('/products', ProductController::class);


// Cart routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/cart', [CartController::class, 'viewCart']);
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::delete('/cart/{productId}', [CartController::class, 'removeItem']);
    Route::put('/cart/decrease/{productId}', [CartController::class, 'decreaseAmount']);
    Route::put('/cart/increase/{productId}', [CartController::class, 'increaseAmount']);
});

// Order routes
Route::middleware(['auth:sanctum'])->resource('/orders', OrderController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::middleware(['checkRole:customer'])->post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
});