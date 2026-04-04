<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriveController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\UrlController;

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
Route::view('message', setting('main_theme', 'default') . '.message')->name('message');
// 授权回调
Route::get('callback', [AuthController::class, 'callback'])->name('callback');
// 登录登出
Route::get('login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
// 后台管理
Route::prefix('admin')->middleware('auth')->group(function () {
    // 安装绑定
    Route::prefix('install')->group(function () {
        Route::any('/', [InstallController::class, 'install'])->name('install');
        Route::any('apply', [InstallController::class, 'apply'])->name('apply');
        Route::any('reset', [InstallController::class, 'reset'])->name('reset');
        Route::any('bind', [InstallController::class, 'bind'])->name('bind');
    });
    // 基础设置
    Route::any('/', [AdminController::class, 'index'])->name('admin');
    Route::any('config', [AdminController::class, 'config'])->name('admin.config');
    Route::any('profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('clear', [AdminController::class, 'clear'])->name('cache.clear');
    // 账号详情
    Route::get('account/list', [AccountController::class, 'list'])->name('admin.account.list');
    Route::get('account/{id}', [AccountController::class, 'quota'])->name('admin.account.info');
    Route::get('account/drive/{id}', [AccountController::class, 'drive'])->name('admin.account.drive');
    Route::any('account/config/{id}', [AccountController::class, 'config'])->name('admin.account.config');
    Route::post('account/remark/{id}', [AccountController::class, 'remark'])->name('admin.account.remark');
    Route::post('account/set-main', [AccountController::class, 'setMain'])->name('admin.account.setMain');
    Route::post('account/delete/{id}', [AccountController::class, 'delete'])->name('admin.account.delete');

    Route::get('url/list', [UrlController::class, 'list'])->name('admin.url.list');
    Route::post('url/delete/{id}', [UrlController::class, 'delete'])->name('admin.url.delete');
    Route::post('url/empty', [UrlController::class, 'empty'])->name('admin.url.empty');


    Route::post('manage/refresh', [ManageController::class, 'refresh'])->name('manage.refresh');
    Route::post('manage/delete', [ManageController::class, 'delete'])->name('manage.delete');
    Route::post('manage/mkdir', [ManageController::class, 'mkdir'])->name('manage.mkdir');
    Route::post('manage/uploadSession', [ManageController::class, 'createUploadSession'])->name('manage.upload');
    Route::any('manage/readme', [ManageController::class, 'createOrUpdateReadme'])->name('manage.readme');
    Route::get('manage/{account_id}/{query?}', [ManageController::class, 'query'])->name('manage.query')->where('query', '.*');
    // 日志
    Route::any('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('admin.logs');

});

Route::post('decrypt', [DriveController::class, 'decrypt'])->name('drive.decrypt');
Route::get('image', [ImageController::class, 'index'])->name('image')->middleware('custom');
Route::post('image-upload', [ImageController::class, 'upload'])->name('image.upload')->middleware('custom');
Route::get('t/{code}', ShareController::class)->name('short');
Route::get('s/{hash}/{item_id}', [DriveController::class, 'download'])->name('download');

Route::post('drive/preload', [DriveController::class, 'preload'])->name('preload');
Route::get('/', [DriveController::class, 'query'])->name('home');
if (setting('single_account_mode', 0)) {
    Route::get('{query?}', [DriveController::class, 'query'])->name('drive.query')->where('query', '.*');
} else {
    Route::get('d/{hash?}/q/{query?}', [DriveController::class, 'query'])->where('query', '.*');
    Route::get('drive/{hash?}/q/{query?}', [DriveController::class, 'query'])->where('query', '.*');
    Route::get('d/{hash?}/{query?}', [DriveController::class, 'query'])->name('drive.query')->where('query', '.*');
    Route::get('drive/{hash?}/{query?}', [DriveController::class, 'query'])->where('query', '.*');
    Route::get('d/{query?}', [DriveController::class, 'query'])->name('drive.single')->where('query', '.*');
    Route::get('drive/{query?}', [DriveController::class, 'query'])->where('query', '.*');
    Route::get('{query?}', [DriveController::class, 'query'])->where('query', '.*');
}