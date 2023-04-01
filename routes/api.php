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
use App\Http\Controllers\User\UserController;

// Users
Route::group(['prefix' => 'user', 'as' => 'user.'], function () {

    Route::post('create', [UserController::class, 'store'])->name('store');

//            Route::get('', [UserController::class, 'show'])->name('show');
//            Route::put('edit', [UserController::class, 'update'])->name('update');
//            Route::post('forgot-password', [UserController::class, 'forgotPassword'])->name('forgot-password');
//            Route::post('reset-password-token', [UserController::class, 'resetPasswordToken'])->name('reset-password-token');
//            Route::delete('', [UserController::class, 'delete'])->name('delete');
//
//            Route::post('login', [UserAuthController::class, 'login'])->name('login');
//            Route::get('logout', [UserAuthController::class, 'logout'])->name('logout');
//
//            Route::get('orders', [UserOrderController::class, 'index'])->name('orders.list');
});

// Files
Route::group(['prefix' => 'file', 'as' => 'file.'], function () {
    Route::post('upload', [FileController::class, 'upload'])->name('upload')
        ->middleware(['auth:api']);
});
