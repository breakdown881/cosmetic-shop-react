<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful endpoints used by the React frontend. All responses are JSON and
| follow the { data, message? } shape so the FE can consume them consistently.
|
*/

Route::apiResource('brands', BrandController::class);
Route::patch('brands/{brand}/status', [BrandController::class, 'updateStatus']);

Route::apiResource('categories', CategoryController::class);
Route::patch('categories/{category}/status', [CategoryController::class, 'updateStatus']);

Route::get('products/search', [ProductController::class, 'search']);
Route::apiResource('products', ProductController::class);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
