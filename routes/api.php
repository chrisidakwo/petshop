<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\FileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\UserController;

// Users
Route::group(['prefix' => 'user', 'as' => 'user.'], function (): void {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('create', [UserController::class, 'store'])->name('store');

    Route::middleware('auth:api')->group(function (): void {
        Route::get('', [UserController::class, 'show'])->name('show');
        Route::put('edit', [UserController::class, 'update'])->name('update');
        Route::delete('', [UserController::class, 'delete'])->name('delete');
    });

    Route::post('forgot-password', [ForgotPasswordController::class, 'index'])
        ->name('forgot-password');

    Route::post('reset-password-token', [ResetPasswordController::class, 'reset'])
        ->name('reset-password-token');

//            Route::get('orders', [UserOrderController::class, 'index'])->name('orders.list');
});

// Files
Route::group(['prefix' => 'file', 'as' => 'file.'], function (): void {
    Route::post('upload', [FileController::class, 'upload'])->name('upload')
        ->middleware(['auth:api']);
});

// Main
Route::group(['prefix' => 'main', 'as' => 'main.'], function () {
    Route::get('promotions', [PromotionController::class])->name('promotions');
    Route::get('blog', [PostController::class, 'listPosts'])->name('posts');
    Route::get('blog/{post}', [PostController::class, 'viewPost'])->name('posts');
});
