<?php

use App\Http\Controllers\Api\ArticlesController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::group([], function () {

    // AUTHENTICATION
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Home
    Route::get('/home', [HomeController::class, 'index']);

    // ARTICALES
    Route::get('/articles', [ArticlesController::class, 'index']);
    Route::get('/articles/{id}', [ArticlesController::class, 'show']);
    Route::get('/search', [ArticlesController::class, 'search']);
    Route::get('/recently-post', [ArticlesController::class, 'recentlyPost']);
    Route::get('/categories', [ArticlesController::class, 'categories']);
    Route::get('/tags', [ArticlesController::class, 'tags']);

    // COMMENTS
    Route::apiResource('/comments', CommentController::class);

});

Route::middleware(['auth:api'])->group(function () {

    // POSTS
    Route::apiResource('/posts', PostController::class);
    Route::post('/post-change-status', [PostController::class, 'changeStatus']);

    // USERS
    Route::apiResource('/users', UserController::class);
    Route::put('/aprove-blogger/{id}', [UserController::class, 'AproveBlogger']);

    // PROFILES
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'update']);

    // RESET PASSWORD
    Route::post('/reset-password', [UserController::class, 'resetPassword']);

    // ROLES
    Route::apiResource('/roles', RoleController::class);
    Route::get('/permissions', [RoleController::class, 'permissions']);

    // NOTIFICATIONS
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [NotificationController::class, 'show']);

});
