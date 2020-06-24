<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\OauthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\DiskController;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// 消息通知
Route::view('message', config('olaindex.theme') . 'message')->name('message');
// 授权回调
Route::get('callback', [OauthController::class, 'callback'])->name('callback');
// 后台
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::prefix('admin')->middleware('auth')->group(static function () {
    // 安装绑定
    Route::prefix('install')->group(static function () {
        Route::any('/', [InstallController::class, 'install'])->name('install');
        Route::any('apply', [InstallController::class, 'apply'])->name('apply');
        Route::any('reset', [InstallController::class, 'reset'])->name('reset');
        Route::any('bind', [InstallController::class, 'bind'])->name('bind');
    });
    // 基础设置
    Route::any('/', [AdminController::class, 'config'])->name('admin.config');
    // 账号详情
    Route::get('/account/list', [AdminController::class, 'account'])->name('admin.account.list');
    Route::get('/account/{id}', [AdminController::class, 'accountDetail'])->name('admin.account.info');
    Route::get('/account/{id}/drive', [AdminController::class, 'driveDetail'])->name('admin.account.drive');
    Route::any('/account/{id}/config', [AdminController::class, 'accountConfig'])->name('admin.account.config');
    Route::post('/account/{id}/remark', [AdminController::class, 'accountRemark'])->name('admin.account.remark');
    Route::post('/account/set-account', [AdminController::class, 'accountSet'])->name('admin.account.set');
    Route::any('logs', [LogViewerController::class, 'index'])->name('admin.logs');
});
// 短网址
Route::get('t/{code}', [IndexController::class])->name('short');
// 多网盘支持
Route::get('d/{hash}', [DiskController::class, 'query'])->name('drive');
Route::get('d/{hash}/q/{query?}', [DiskController::class, 'query'])->name('drive.query')->where('query', '.*');
Route::get('d/{hash}/id/{query?}', [DiskController::class, 'query'])->name('drive.query.id');
// 搜索
Route::get('d/{hash}/search', [DiskController::class, 'search'])->name('drive.search');
// 图床
Route::get('image', [ImageController::class, 'index'])->name('image');
Route::post('image-upload', [ImageController::class, 'upload'])->name('image.upload');
// 首页
Route::get('/', [HomeController::class])->name('home');


