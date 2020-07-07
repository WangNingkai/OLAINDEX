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
Route::get('callback', 'OauthController@callback')->name('callback');
// 后台
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::prefix('admin')->middleware('auth')->group(static function () {
    // 安装绑定
    Route::prefix('install')->group(static function () {
        Route::any('/', 'InstallController@install')->name('install');
        Route::any('apply', 'InstallController@apply')->name('apply');
        Route::any('reset', 'InstallController@reset')->name('reset');
        Route::any('bind', 'InstallController@bind')->name('bind');
    });
    // 基础设置
    Route::any('/', 'AdminController@config')->name('admin.config');
    Route::any('profile', 'AdminController@profile')->name('admin.profile');
    // 账号详情
    Route::get('account/list', 'AdminController@account')->name('admin.account.list');
    Route::get('account/{id}', 'AdminController@accountDetail')->name('admin.account.info');
    Route::get('account/{id}/drive', 'AdminController@driveDetail')->name('admin.account.drive');
    Route::any('account/{id}/config', 'AdminController@accountConfig')->name('admin.account.config');
    Route::post('account/{id}/remark', 'AdminController@accountRemark')->name('admin.account.remark');
    Route::post('account/set-account', 'AdminController@accountSet')->name('admin.account.set');
    Route::post('account/delete', 'AdminController@accountDelete')->name('admin.account.set');
    Route::any('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('admin.logs');
});
// 短网址
Route::get('t/{code}', 'IndexController')->name('short');
// 多网盘支持
Route::get('d/{hash}', 'DiskController@query')->name('drive');
Route::get('d/{hash}/q/{query?}', 'DiskController@query')->name('drive.query')->where('query', '.*');
Route::get('d/{hash}/id/{query?}', 'DiskController@query')->name('drive.query.id');
// 搜索
Route::get('d/{hash}/search', 'DiskController@search')->name('drive.search');
// 图床
Route::get('image', 'ImageController@index')->name('image');
Route::post('image-upload', 'ImageController@upload')->name('image.upload');
// 首页
Route::get('/', 'HomeController')->name('home');


