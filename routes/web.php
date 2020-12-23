<?php
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
Route::get('callback', 'AuthController@callback')->name('callback');
// 登录登出
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// 后台管理
Route::prefix('admin')->middleware('auth')->group(static function () {
    // 安装绑定
    Route::prefix('install')->group(static function () {
        Route::any('/', 'InstallController@install')->name('install');
        Route::any('apply', 'InstallController@apply')->name('apply');
        Route::any('reset', 'InstallController@reset')->name('reset');
        Route::any('bind', 'InstallController@bind')->name('bind');
    });
    // 基础设置
    Route::any('/', 'AdminController@config');
    Route::any('config', 'AdminController@config')->name('admin.config');
    Route::any('profile', 'AdminController@profile')->name('admin.profile');
    Route::get('clear', 'AdminController@clear')->name('cache.clear');
    // 账号详情
    Route::get('account/list', 'AccountController@list')->name('admin.account.list');
    Route::get('account/{id}', 'AccountController@quota')->name('admin.account.info');
    Route::get('account/drive/{id}', 'AccountController@drive')->name('admin.account.drive');
    Route::any('account/config/{id}', 'AccountController@config')->name('admin.account.config');
    Route::post('account/remark/{id}', 'AccountController@remark')->name('admin.account.remark');
    Route::post('account/set-main', 'AccountController@setMain')->name('admin.account.setMain');
    Route::post('account/delete', 'AccountController@delete')->name('admin.account.delete');

    Route::any('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('admin.logs');

    Route::any('manage/{hash}/q/{query?}', 'ManageController@query')->name('admin.file.manage')->where('query', '.*');
    Route::any('manage/{hash}/edit/{query?}', 'ManageController@edit')->name('admin.file.edit');
    Route::any('manage/{hash}/create/{query?}', 'ManageController@create')->name('admin.file.create');
    Route::post('manage/delete', 'ManageController@delete')->name('admin.file.delete');
    Route::post('manage/hide', 'ManageController@hideItem')->name('admin.file.hide');
    Route::post('manage/encrypt', 'ManageController@encryptItem')->name('admin.file.encrypt');
});
// 分享短链
Route::get('t/{code}', 'IndexController')->name('short');
// 多网盘支持
Route::get('d/{hash}', 'DiskController@query')->name('drive');
Route::get('d/{hash}/q/{query?}', 'DiskController@query')->name('drive.query')->where('query', '.*');
Route::get('d/{hash}/id/{query?}', 'DiskController@query')->name('drive.query.id');
// 搜索
Route::get('d/{hash}/search', 'DiskController@search')->name('drive.search');
// 加密
Route::post('decrypt', 'DiskController@decrypt')->name('drive.decrypt');
// 图床
Route::get('image', 'ImageController@index')->name('image')->middleware('custom');
Route::post('image-upload', 'ImageController@upload')->name('image.upload')->middleware('custom');
// 首页
Route::get('/', 'HomeController')->name('home');


