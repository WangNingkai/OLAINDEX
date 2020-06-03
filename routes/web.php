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
Route::get('/callback', 'OauthController@callback')->name('callback');
// 首页
Route::get('/', 'HomeController')->name('home');
// 后台
Route::prefix('admin')->group(static function () {
    // 安装绑定
    Route::prefix('install')->group(static function () {
        Route::any('/', 'InstallController@install')->name('install');
        Route::any('apply', 'InstallController@apply')->name('apply');
        Route::any('reset', 'InstallController@reset')->name('reset');
        Route::any('bind', 'InstallController@bind')->name('bind');
    });
    // 基础设置
    Route::any('/', 'AdminController@config')->name('admin.config');
    // 账号详情
    Route::any('/account/list', 'AdminController@account')->name('admin.account.list');
    Route::any('/account/{id}', 'AdminController@accountDetail')->name('admin.account.info');
});
// 短网址
Route::get('/{code}', 'IndexController')->name('short');
