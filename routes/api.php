<?php

use App\Http\Controllers\Api\Admin\CustomerController as ApiCustomerController;
use App\Http\Controllers\Api\Admin\DiscountController as ApiDiscountController;
use App\Http\Controllers\Api\Admin\FeeShipController as ApiFeeShipController;
use App\Http\Controllers\Api\Admin\OrderController as ApiOrderController;
use App\Http\Controllers\Api\Admin\ProductCommentController as ApiProductCommentController;
use App\Http\Controllers\Api\Admin\RoleController as ApiRoleController;
use App\Http\Controllers\Api\Admin\StaffController as ApiStaffController;
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
| Authenticated CRUD API for the React frontend. Authorization matrix:
| MANAGER: all CRUD; ADMIN: catalog CRUD; STAFF: order APIs only.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('admin.role:MANAGER,ADMIN,STAFF')->group(function () {
        Route::apiResource('customers', ApiCustomerController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::apiResource('discounts', ApiDiscountController::class)->only(['index', 'show']);
        Route::apiResource('feeships', ApiFeeShipController::class)->only(['index', 'show']);
        Route::apiResource('orders', ApiOrderController::class);
    });

    Route::middleware('admin.role:MANAGER,ADMIN')->group(function () {
        Route::get('products/{product}/comments', [ApiProductCommentController::class, 'index']);
        Route::patch('products/{product}/comments/{comment}', [ApiProductCommentController::class, 'update']);

        Route::apiResource('discounts', ApiDiscountController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('feeships', ApiFeeShipController::class)->only(['store', 'update', 'destroy']);

        Route::apiResource('brands', BrandController::class);
        Route::patch('brands/{brand}/status', [BrandController::class, 'updateStatus']);

        Route::apiResource('categories', CategoryController::class);
        Route::patch('categories/{category}/status', [CategoryController::class, 'updateStatus']);

        Route::get('products/search', [ProductController::class, 'search']);
        Route::patch('products/{product}/status', [ProductController::class, 'updateStatus']);
        Route::apiResource('products', ProductController::class);
    });

    Route::middleware('admin.role:MANAGER')->group(function () {
        Route::apiResource('staffs', ApiStaffController::class);
        Route::apiResource('roles', ApiRoleController::class);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
