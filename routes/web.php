<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\SpaController;
use App\Http\Controllers\Api\Admin\CustomerController as ApiCustomerController;
use App\Http\Controllers\Api\Admin\DashboardController as ApiDashboardController;
use App\Http\Controllers\Api\Admin\DiscountController as ApiDiscountController;
use App\Http\Controllers\Api\Admin\FeeShipController as ApiFeeShipController;
use App\Http\Controllers\Api\Admin\MediaController as ApiMediaController;
use App\Http\Controllers\Api\Admin\NewsletterController as ApiNewsletterController;
use App\Http\Controllers\Api\Admin\OrderController as ApiOrderController;
use App\Http\Controllers\Api\Admin\ProductCommentController as ApiProductCommentController;
use App\Http\Controllers\Api\Admin\RoleController as ApiRoleController;
use App\Http\Controllers\Api\Admin\StaffController as ApiStaffController;
use App\Http\Controllers\Api\BrandController as ApiBrandController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return \App\Support\PublicReactShell::welcome();
});

Route::prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('admin.login.form');
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');

    Route::middleware(['admin'])->group(function () {
        Route::prefix('api')->name('admin.api.')->group(function () {
            Route::get('dashboard', [ApiDashboardController::class, 'index'])
                ->name('dashboard')
                ->middleware('admin.role:MANAGER,ADMIN,STAFF');
            Route::apiResource('brands', ApiBrandController::class)
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::patch('brands/{brand}/status', [ApiBrandController::class, 'updateStatus'])
                ->name('brands.status')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::apiResource('categories', ApiCategoryController::class)
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::patch('categories/{category}/status', [ApiCategoryController::class, 'updateStatus'])
                ->name('categories.status')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::get('products/search', [ApiProductController::class, 'search'])
                ->name('products.search')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::apiResource('products', ApiProductController::class)
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::patch('products/{product}/status', [ApiProductController::class, 'updateStatus'])
                ->name('products.status')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::get('media', [ApiMediaController::class, 'index'])
                ->name('media.index')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::post('media', [ApiMediaController::class, 'store'])
                ->name('media.store')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::delete('media/{media}', [ApiMediaController::class, 'destroy'])
                ->name('media.destroy')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::get('newsletters', [ApiNewsletterController::class, 'index'])
                ->name('newsletters.index')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::post('newsletters/send', [ApiNewsletterController::class, 'send'])
                ->name('newsletters.send')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::get('order-options', [ApiOrderController::class, 'options'])
                ->name('orders.options')
                ->middleware('admin.role:MANAGER,ADMIN,STAFF');
            Route::apiResource('orders', ApiOrderController::class)
                ->middleware('admin.role:MANAGER,ADMIN,STAFF');
            Route::apiResource('customers', ApiCustomerController::class)
                ->only(['index', 'show', 'update', 'destroy'])
                ->middleware('admin.role:MANAGER,ADMIN,STAFF');
            Route::apiResource('discounts', ApiDiscountController::class)
                ->only(['index', 'show'])
                ->middleware('admin.role:MANAGER,ADMIN,STAFF');
            Route::apiResource('discounts', ApiDiscountController::class)
                ->only(['store', 'update', 'destroy'])
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::apiResource('feeships', ApiFeeShipController::class)
                ->only(['index', 'show'])
                ->middleware('admin.role:MANAGER,ADMIN,STAFF');
            Route::apiResource('feeships', ApiFeeShipController::class)
                ->only(['store', 'update', 'destroy'])
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::get('products/{product}/comments', [ApiProductCommentController::class, 'index'])
                ->name('products.comments.index')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::patch('products/{product}/comments/{comment}', [ApiProductCommentController::class, 'update'])
                ->name('products.comments.update')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::get('comments', [ApiProductCommentController::class, 'all'])
                ->name('comments.index')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::patch('comments/{comment}', [ApiProductCommentController::class, 'updateAny'])
                ->name('comments.update')
                ->middleware('admin.role:MANAGER,ADMIN');
            Route::apiResource('roles', ApiRoleController::class)->middleware('admin.role:MANAGER');
            Route::apiResource('staffs', ApiStaffController::class)->middleware('admin.role:MANAGER');
        });

        Route::get('{path?}', [SpaController::class, 'index'])
            ->where('path', '.*')
            ->name('admin.spa');
    });
});
