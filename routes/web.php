<?php

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
Route::get('/callback', 'OauthController@callback')->name('callback');
Route::get('/', 'IndexController')->name('home');
Route::prefix('admin')->group(static function () {
    // 安装
    Route::prefix('install')->group(static function () {
        Route::any('/', 'InstallController@install')->name('install');
        Route::any('apply', 'InstallController@apply')->name('apply');
        Route::any('reset', 'InstallController@reset')->name('reset');
        Route::any('bind', 'InstallController@bind')->name('bind');
    });
    // 基础设置
    Route::any('/', 'AdminController@config')->name('admin.config');
});
