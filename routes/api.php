<?php

use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\BlogMediaContentsController;
use App\Http\Controllers\Api\BlogsController;
use Database\Seeders\BlogMediaContentsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Users Authentication
Route::post('register', [UsersController::class, 'register']);
Route::post('login', [UsersController::class, 'login']);

// Public Blog Display Routes
Route::get('blogs', [BlogsController::class, 'index']);
Route::get('blogs/{blog}', [BlogsController::class, 'show']);

// Blog Media Display Route
Route::get('blog-image', [BlogMediaContentsController::class, 'index']);

// Protected Routes (require authentication)
Route::group(['middleware' => ['auth:api']], function () {

    // User's Profile and Account Management
    Route::get('profile', [UsersController::class, 'profile']);
    Route::put('edit-profile', [UsersController::class, 'editProfile']);
    Route::post('change-password', [UsersController::class, 'changePassword']);
    Route::get('logout', [UsersController::class, 'logout']);

    // Blog Management (CRUD operations)
    Route::post('blogs', [BlogsController::class, 'store']);
    Route::put('/blogs/{blog}', [BlogsController::class, 'update']);
    Route::delete('/blogs/{blog}', [BlogsController::class, 'destroy']);

    // Blog Media Management
    Route::post('blog-image', [BlogMediaContentsController::class, 'store']);
    Route::delete('blog-image', [BlogMediaContentsController::class, 'destroy']);
});
