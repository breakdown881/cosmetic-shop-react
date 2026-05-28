<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\FeeShipController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCommentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Api\Admin\CustomerController as ApiCustomerController;
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
        Route::get('/', [DashBoardController::class, 'index'])->name('admin.dashboard');

        Route::prefix('orders')->middleware('admin.role:MANAGER,ADMIN,STAFF')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('admin.order.index');
            Route::get('create', [OrderController::class, 'index'])->name('admin.order.create');
            Route::get('edit/{order}', [OrderController::class, 'index'])->name('admin.order.edit');
        });

        Route::prefix('brands')->middleware('admin.role:MANAGER,ADMIN')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('admin.brand.index');
            Route::get('create', [BrandController::class, 'index'])->name('admin.brand.create');
            Route::get('edit/{id}', [BrandController::class, 'index'])->name('admin.brand.edit');
        });

        Route::prefix('categories')->middleware('admin.role:MANAGER,ADMIN')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category.index');
            Route::get('create', [CategoryController::class, 'index'])->name('admin.category.create');
            Route::get('edit/{id}', [CategoryController::class, 'index'])->name('admin.category.edit');
        });

        Route::prefix('products')->middleware('admin.role:MANAGER,ADMIN')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('admin.product.index');
            Route::get('create', [ProductController::class, 'index'])->name('admin.product.create');
            Route::get('edit/{id}', [ProductController::class, 'index'])->name('admin.product.edit');
            Route::get('{product}/comments', [ProductCommentController::class, 'index'])->name('admin.product.comments.index');
        });

        Route::prefix('comments')->middleware('admin.role:MANAGER,ADMIN')->group(function () {
            Route::get('/', [ProductCommentController::class, 'all'])->name('admin.comments.index');
        });

        Route::prefix('images')->middleware('admin.role:MANAGER,ADMIN')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('admin.media.index');
        });

        Route::prefix('newsletters')->middleware('admin.role:MANAGER,ADMIN')->group(function () {
            Route::get('/', [NewsletterController::class, 'index'])->name('admin.newsletter.index');
        });

        Route::prefix('roles')->middleware('admin.role:MANAGER')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('admin.role.index');
            Route::get('create', [RoleController::class, 'index'])->name('admin.role.create');
            Route::get('edit/{role}', [RoleController::class, 'index'])->name('admin.role.edit');
        });

        Route::prefix('staffs')->middleware('admin.role:MANAGER')->group(function () {
            Route::get('/', [StaffController::class, 'index'])->name('admin.staff.index');
            Route::get('create', [StaffController::class, 'index'])->name('admin.staff.create');
            Route::get('edit/{staff}', [StaffController::class, 'index'])->name('admin.staff.edit');
        });

        Route::prefix('customers')->middleware('admin.role:MANAGER,ADMIN,STAFF')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('admin.customer.index');
            Route::get('edit/{customer}', [CustomerController::class, 'index'])->name('admin.customer.edit');
        });

        Route::prefix('feeships')->group(function () {
            Route::get('/', [FeeShipController::class, 'index'])
                ->middleware('admin.role:MANAGER,ADMIN,STAFF')
                ->name('admin.feeship.index');
            Route::get('create', [FeeShipController::class, 'index'])
                ->middleware('admin.role:MANAGER,ADMIN')
                ->name('admin.feeship.create');
            Route::get('edit/{feeship}', [FeeShipController::class, 'index'])
                ->middleware('admin.role:MANAGER,ADMIN')
                ->name('admin.feeship.edit');
        });

        Route::prefix('discounts')->group(function () {
            Route::get('/', [DiscountController::class, 'index'])
                ->middleware('admin.role:MANAGER,ADMIN,STAFF')
                ->name('admin.discount.index');
            Route::get('create', [DiscountController::class, 'index'])
                ->middleware('admin.role:MANAGER,ADMIN')
                ->name('admin.discount.create');
            Route::get('edit/{discount}', [DiscountController::class, 'index'])
                ->middleware('admin.role:MANAGER,ADMIN')
                ->name('admin.discount.edit');
        });

        Route::prefix('api')->name('admin.api.')->group(function () {
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
    });
});
