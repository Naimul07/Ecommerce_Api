<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::apiResource('/admin/products', ProductController::class);
    Route::apiResource('/admin/category', CategoryController::class); 
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/products',[ProductController::class,'index']);
    Route::apiResource('/order',OrderController::class);
});
