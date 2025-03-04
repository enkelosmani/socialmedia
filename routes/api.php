<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

// Authentication routes (assuming these exist based on Postman success)
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

// Add this route to return the authenticated user
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Protected routes (assuming these are already defined)
Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('posts', PostController::class);
    Route::post('/posts/{post}/likes', [LikeController::class, 'store']);
    Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);

    Route::apiResource('users', UserController::class)->only(['index', 'show', 'update']);
    Route::apiResource('contents', ContentController::class);
});
