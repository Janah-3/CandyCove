<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\authController;
use \App\Http\Controllers\ProductController;
use \App\Http\Controllers\CartController;
use \App\Http\Controllers\OrderController;
use \App\Http\Controllers\categoryController;
use \App\Http\Controllers\addressController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//  Auth routes
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/login', [authController::class, 'login']);
    Route::post('/register', [authController::class, 'register']);
    Route::post('/forgetPass', [authController::class, 'forgetPass']);
});

Route::put('/resetPass', [authController::class, 'resetPass']);
Route::middleware(['auth:sanctum'])->post('/logout', [authController::class, 'logout']);
Route::middleware(['auth:sanctum', 'checkRole:admin'])->post('/addAdmin', [authController::class, 'AddAdmin']);

//  Product routes 
Route::apiResource('/products', ProductController::class);

// Cart routes
Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    Route::get('/cart', [CartController::class, 'viewCart']);
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::delete('/cart/clear', [CartController::class, 'clearCart']);
    Route::delete('/cart/{productId}', [CartController::class, 'removeItem']);
    Route::put('/cart/decrease/{productId}', [CartController::class, 'decreaseAmount']);
    Route::put('/cart/increase/{productId}', [CartController::class, 'increaseAmount']);
});

// Order routes 
Route::middleware(['auth:sanctum', 'throttle:20,1'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
});

// Category routes 
Route::apiResource('/categories', categoryController::class);

//Address routes 
Route::middleware(['auth:sanctum', 'checkRole:customer'])->apiResource('/addresses', addressController::class);
Route::middleware(['auth:sanctum', 'checkRole:customer'])->get('/user/addresses', [addressController::class, 'getUserAddresses']);

Route::get('/email/verify/{id}/{hash}', [authController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

Route::post('/login/remember', [authController::class, 'loginWithRememberToken']);
