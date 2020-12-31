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
Route::view('message', setting('main_theme', 'default') . '.message')->name('message');
// 授权回调
Route::get('callback', 'AuthController@callback')->name('callback');
// 登录登出
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// 后台管理
Route::prefix('admin')->middleware('auth')->group(function () {
    // 安装绑定
    Route::prefix('install')->group(function () {
        Route::any('/', 'InstallController@install')->name('install');
        Route::any('apply', 'InstallController@apply')->name('apply');
        Route::any('reset', 'InstallController@reset')->name('reset');
        Route::any('bind', 'InstallController@bind')->name('bind');
    });
    // 基础设置
    Route::any('/', 'AdminController@index')->name('admin');
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
    Route::post('account/delete/{id}', 'AccountController@delete')->name('admin.account.delete');

    Route::get('url/list', 'UrlController@list')->name('admin.url.list');
    Route::post('url/delete/{id}', 'UrlController@delete')->name('admin.url.delete');
    Route::post('url/empty', 'UrlController@empty')->name('admin.url.empty');


    Route::post('manage/refresh', 'ManageController@refresh')->name('manage.refresh');
    Route::post('manage/delete', 'ManageController@delete')->name('manage.delete');
    Route::post('manage/mkdir', 'ManageController@mkdir')->name('manage.mkdir');
    Route::post('manage/uploadSession', 'ManageController@createUploadSession')->name('manage.upload');
    Route::any('manage/readme', 'ManageController@createOrUpdateReadme')->name('manage.readme');
    Route::get('manage/{account_id}/{query?}', 'ManageController@query')->name('manage.query')->where('query', '.*');
    // 日志
    Route::any('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('admin.logs');

});

Route::post('decrypt', 'DriveController@decrypt')->name('drive.decrypt');
Route::get('image', 'ImageController@index')->name('image')->middleware('custom');
Route::post('image-upload', 'ImageController@upload')->name('image.upload')->middleware('custom');
Route::get('t/{code}', 'ShareController')->name('short');
Route::get('s/{hash}/{item_id}', 'DriveController@download')->name('download');

Route::post('drive/preload', 'DriveController@preload')->name('preload');
Route::get('/', 'DriveController@query')->name('home');
if (setting('single_account_mode', 0)) {
    Route::get('{query?}', 'DriveController@query')->name('drive.query')->where('query', '.*');
} else {
    Route::get('d/{hash?}/q/{query?}', 'DriveController@query')->where('query', '.*');
    Route::get('drive/{hash?}/q/{query?}', 'DriveController@query')->where('query', '.*');
    Route::get('d/{hash?}/{query?}', 'DriveController@query')->name('drive.query')->where('query', '.*');
    Route::get('drive/{hash?}/{query?}', 'DriveController@query')->where('query', '.*');
    Route::get('d/{query?}', 'DriveController@query')->name('drive.single')->where('query', '.*');
    Route::get('drive/{query?}', 'DriveController@query')->where('query', '.*');
    Route::get('{query?}', 'DriveController@query')->where('query', '.*');
}

