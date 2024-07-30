<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MyController;
use App\Http\Controllers\Auth\SettingController;
use App\Http\Controllers\Order\ImageController;
use App\Http\Controllers\Order\OccasionsController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\PackagerController;
use App\Http\Controllers\Order\StatusController;
use App\Http\Controllers\Order\SubstitutionsController;
use App\Http\Controllers\User\ColumnController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaSetting;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login'])->name('user.login');
Route::post('login/worker', [AuthController::class, 'loginWorker'])->name('user.login.worker');

Route::group(['middleware' => 'auth:sanctum'], function () {
    // auth
    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');
    Route::post('my/code', [MyController::class, 'createCode'])->name('my.createCode');
    Route::post('my/settings', [MyController::class, 'saveSettings'])->name('my.settings');
    Route::post('settings', [SettingController::class, 'saveSetting'])->name('settings.store')
        ->can('create', BlcaSetting::class);
    Route::delete('settings/{setting}', [SettingController::class, 'removeSetting'])->name('settings.remove')
        ->can('delete', BlcaSetting::class);
    Route::get('settings', [SettingController::class, 'listSettings'])->name('settings.list');
    Route::post('settings/logout', [SettingController::class, 'logoutAllUsers'])->name('settings.logout.all')
        ->can('create', BlcaSetting::class);;

    //order
    Route::get('orders', [OrderController::class, 'list'])->name('user.order.list');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('user.order.show');
    Route::post('order/in-packaging', [PackagerController::class, 'inPackaging'])->name('order.packaging');
    Route::post('order/confirm-set', [PackagerController::class, 'confirmSet'])->name('order.confirm.set');
    Route::post('order/packaged/{order}', [PackagerController::class, 'packaged'])->name('order.packaged');
    Route::get('occasions', [OccasionsController::class, 'list'])->name('occasion.list');
    Route::post('columns', [ColumnController::class, 'store'])->name('user.column.store');
    Route::get('statuses', [StatusController::class, 'list'])->name('order.status.list');

    // product
    Route::get('substitutions/ingredient/{ingredient}', [SubstitutionsController::class, 'list'])->name('ingredients.substitutions.list');
    Route::post('substitutions/ingredient/{ingredient}/hard', [SubstitutionsController::class, 'saveHardSubstitution'])->name('ingredients.substitutions.hard');
    Route::post('substitutions/ingredient/{ingredient}/soft', [SubstitutionsController::class, 'saveSoftSubstitution'])->name('ingredients.substitutions.soft');
    Route::post('substitutions/product/{product}', [SubstitutionsController::class, 'saveProductSubstitution'])->name('product.substitutions');

    // image
    Route::post('image/history/{history}', [ImageController::class, 'addHistoryImage'])->name('history.image.add');
    Route::delete('image/history/{historyImage}', [ImageController::class, 'deleteHistoryImage'])->name('history.image.delete');

    // user
    Route::get('user/warehouses', [WarehouseController::class, 'userWarehousesList'])->name('user.warehouse.list');
    Route::get('user/roles', [UserController::class, 'availableRoles'])->name('user.roles.list')
        ->can('create', User::class);;
    Route::get('user/exists', [UserController::class, 'exist'])->name('user.user.exist')
        ->can('view', User::class);
    Route::put('user/warehouse/{user}', [UserController::class, 'assignWarehouses'])->name('user.user.warehouses')
        ->can('create', User::class);
    Route::put('user/roles/{user}', [UserController::class, 'assignRoles'])->name('user.roles')
        ->can('create', User::class);
    Route::post('users', [UserController::class, 'create'])->name('user.create')
        ->can('create', User::class);
    Route::put('users/{user}', [UserController::class, 'update'])->name('user.update')
        ->can('create', User::class);
    Route::get('users', [UserController::class, 'list'])->name('user.list');
});

