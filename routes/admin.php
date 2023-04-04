<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "admin" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth:api', 'admin']], function (): void {
    Route::get('user-listing', [UserController::class, 'index'])->name('users.listing');
    Route::post('create', [AdminController::class, 'store'])->name('store');
    Route::put('user-edit/{user}', [AdminController::class, 'update'])->name('update');
    Route::delete('user-delete/{user}', [AdminController::class, 'delete'])->name('delete');
});
