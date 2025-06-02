<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\BlogMediaContentsController;
use App\Http\Controllers\Api\BlogsController;
use Database\Seeders\BlogMediaContentsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::group(["middleware" => ["auth:api"]], function () {
    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("refresh-token", [ApiController::class, "refreshToken"]);
    Route::get("logout", [ApiController::class, "logout"]);
    Route::post("change-password", [ApiController::class, "changePassword"]);

    Route::post("blogs", [BlogsController::class, "store"]);
    Route::put('/blogs/{blog}', [BlogsController::class, 'update']);
    Route::delete('/blogs/{blog}', [BlogsController::class, 'destroy']);

    Route::post('blog-image', [BlogMediaContentsController::class, "store"]);
    Route::delete('blog-image', [BlogMediaContentsController::class, "destroy"]);
});
Route::get("blogs", [BlogsController::class, "index"]);
Route::get('blogs/{blog}', [BlogsController::class, 'show']);
Route::get('blog-image', [BlogMediaContentsController::class, "index"]);
