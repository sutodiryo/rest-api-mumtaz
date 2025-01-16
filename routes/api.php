<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [LoginController::class, 'login'])->name('login');
});

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Post
    Route::apiResource('/post', PostController::class);

    Route::prefix('post')->as('post.')->group(function () {
        Route::post('/tags/{id}', [PostController::class, 'tags'])->name('tags');
        Route::post('/images/{id}', [PostController::class, 'images'])->name('images');
    });

    // Product
    Route::apiResource('/product', ProductController::class);

    Route::prefix('product')->as('product.')->group(function () {
        Route::post('/tags/{id}', [ProductController::class, 'tags'])->name('tags');
        Route::post('/images/{id}', [ProductController::class, 'images'])->name('images');
    });

    // Transaction
    Route::apiResource('/transaction', TransactionController::class);
});
