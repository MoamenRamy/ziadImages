<?php

use App\Http\Controllers\api\AdminController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\ImageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Middleware\admin;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


    Route::get('/categories', [CategoryController::class, 'index']); //test middleware
    Route::get('/category/{id}', [CategoryController::class, 'show']); //

    Route::get('/images', [ImageController::class, 'index']); //
    Route::get('/image/{id}', [ImageController::class, 'show']); //

    Route::group(['prefix' => 'admin' , 'middleware' => ['auth:sanctum', 'admin']], function () {

        Route::get('/', [AdminController::class, 'getAdmins']); //
        Route::post('/make', [AdminController::class, 'makeAdmin']); //

        Route::get('/users', [AdminController::class, 'index']); //
        Route::get('/user/{id}', [AdminController::class, 'show']); //
        Route::delete('/user/{id}', [AdminController::class, 'destroy']); //

        Route::post('/categories', [CategoryController::class, 'store']); //
        Route::put('/categories/{id}', [CategoryController::class, 'update']); //
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
        Route::post('/categories/updateImage', [CategoryController::class, 'updateImage']);
        // Route::delete('/categories/image/delete', [CategoryController::class, 'deleteImage']);

        Route::post('/images', [ImageController::class, 'store']); //
        Route::put('/images/{id}', [ImageController::class, 'update']); //
        Route::delete('/images/{id}', [ImageController::class, 'destroy']); //
        Route::post('/updateImages', [ImageController::class, 'updateImages']);

    });


    require __DIR__.'/auth.php';

?>
