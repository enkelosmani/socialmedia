<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('posts',PostController::class);
Route::post('/posts/{post}/likes', [LikeController::class, 'store']);
Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy']);
Route::post('posts/{post}/comments',[CommentController::class , 'store']);
Route::apiResource('contents',ContentController::class);
Route::apiResource('users',UserController::class);



