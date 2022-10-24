<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Products\ProductPurchaseController;
use App\Http\Controllers\Users\UserController;
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

Route::group(['middleware' => 'api'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('', [UserController::class, 'auth'])->name('users.view');
        Route::post('', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}', [UserController::class, 'view'])->name('users.update');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'delete'])->name('users.delete');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::post('', [ProductController::class, 'store'])->name('products.store');
        Route::get('{product}', [ProductController::class, 'view'])->name('products.view');
        Route::put('{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('{product}', [ProductController::class, 'delete'])->name('products.delete');
    });

    Route::post('/deposit', [UserController::class, 'deposit'])->name('users.deposit');
    Route::post('/reset', [UserController::class, 'resetDeposit'])->name('users.deposit.reset');

    Route::post('/buy', [ProductPurchaseController::class, 'purchase'])->name('buy');
});
