<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Influencer\ProductController as InfluencerProductController;
use App\Http\Controllers\Influencer\LinkController as InfluencerLinkController;

use App\Http\Controllers\Checkout\LinkController as CheckoutLinkController;
use App\Http\Controllers\Checkout\OrderController as CheckoutOrderController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy builzding your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group([
    'middleware' => 'auth:api',
], function () {    
    Route::get('user', [AuthController::class, 'user']);
    Route::put('user/info', [AuthController::class, 'update_info']);
    Route::put('user/password', [AuthController::class, 'update_password']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group([
    'middleware' => ['auth:api', 'scope:admin'],
    'prefix' => 'admin'
], function() {
    Route::get('chart', [DashboardController::class, 'chart']);
    Route::post('upload', [ImageController::class, 'upload']);
    Route::get('export', [OrderController::class, 'export']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('orders', OrderController::class)->only('index', 'show');
    Route::apiResource('permissions', PermissionController::class)->only('index');
});

Route::group([
    'prefix' => 'influencer',
], function() {
    Route::get('products', [InfluencerProductController::class, 'index']);

    Route::group([
        'middleware' => ['auth:api', 'scope:influencer'],
    ], function() {
        Route::post('links', [InfluencerLinkController::class, 'store']);
    });
});

Route::group([
    'prefix' => 'checkout',
    'namespace' => 'Checkout'
], function () {
    Route::get('links/{code}', [CheckoutLinkController::class, 'show']);
    Route::post('orders', [CheckoutOrderController::class, 'store']);
});