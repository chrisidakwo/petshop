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

Route::group(['middleware' => 'auth:api'],  function () {

    // Files
    Route::prefix('file')->as('file.')->group(function () {
        Route::post('upload', [FileController::class, 'upload'])->name('upload');
    });
});
