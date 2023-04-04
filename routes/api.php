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
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\User\ResetPasswordController;
use App\Http\Controllers\User\ForgotPasswordController;

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
});

// Products
Route::get('products', [ProductController::class, 'index'])->name('products');
Route::post('product/create', [ProductController::class, 'store'])->name('product.store');
Route::resource('product', ProductController::class)->only(['show',  'update', 'destroy']);

// Order Statuses
Route::get('order-statuses', [OrderStatusController::class, 'index'])->name('order-statuses');

// Orders
Route::middleware('auth:api')->group(function (): void {
    Route::group(['prefix' => 'orders'], function (): void {
        Route::get('', [OrderController::class, 'index'])->name('orders');
//    Route::get('dashboard', [OrderController::class, 'dashboard'])->name('orders.dashboard');
//    Route::get('shipment-locator', [OrderController::class, 'shipmentLocator'])->name('orders.shipment-locator');
    });
    Route::post('order/create', [OrderController::class, 'store'])->name('order.store');
    Route::resource('order', OrderController::class)->only(['show', 'update', 'destroy']);
    Route::get('order/{order}/download', [OrderController::class, 'download'])->name('order.download');
});

// Payments
Route::middleware('auth:api')->group(function (): void {
    Route::post('payment/create', [PaymentController::class, 'store'])->name('payment.store');
});

// Categories
Route::get('categories', [CategoryController::class, 'index'])->name('categories');

// Brands
Route::get('brands', [BrandController::class, 'index'])->name('brands');
Route::group(['prefix' => 'brand', 'as' => 'brand.'], function (): void {
    Route::get('{brand}', [BrandController::class, 'show'])->name('show');
    Route::put('{brand}', [BrandController::class, 'update'])->name('update');
    Route::delete('{brand}', [BrandController::class, 'delete'])->name('delete');
});

// Files
Route::group(['prefix' => 'file', 'as' => 'file.'], function (): void {
    Route::post('upload', [FileController::class, 'upload'])->name('upload')
        ->middleware(['auth:api']);
});

// Main
Route::group(['prefix' => 'main', 'as' => 'main.'], function (): void {
    Route::get('promotions', [PromotionController::class])->name('promotions');
    Route::get('blog', [PostController::class, 'listPosts'])->name('posts');
    Route::get('blog/{post}', [PostController::class, 'viewPost'])->name('posts');
});
