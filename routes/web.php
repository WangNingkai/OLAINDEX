<?php

use Illuminate\Support\Facades\Route;

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

// 授权
Route::get('/oauth', 'OauthController@oauth')->name('oauth');
// 安装
Route::prefix('install')->group(function () {
    Route::any('/', 'InstallController@install')->name('_1stInstall');
    Route::any('apply', 'InstallController@apply')->name('apply');
    Route::any('reset', 'InstallController@reset')->name('reset');
    Route::any('bind', 'InstallController@bind')->name('bind');
});
// 索引
Route::any('/', function () {
    $redirect = (int)\App\Helpers\Tool::config('image_home', 0) ? 'image'
        : 'home';

    return redirect()->route($redirect);
});
Route::prefix('home')->group(function () {
    Route::get('{query?}', 'IndexController@list')->where('query', '.*')
        ->name('home');
});
Route::get('show/{query}', 'IndexController@show')->where('query', '.*')
    ->name('show');
Route::get('down/{query}', 'IndexController@download')->where('query', '.*')
    ->name('download')->middleware('hotlinkProtection');
Route::get('view/{query}', 'IndexController@view')->where('query', '.*')
    ->name('view')->middleware('hotlinkProtection');
Route::post('password', 'IndexController@handlePassword')->name('password');
Route::get('thumb/{id}/size/{size}', 'IndexController@thumb')->name('thumb');
Route::get('thumb/{id}/{width}/{height}', 'IndexController@thumbCrop')
    ->name('thumb_crop');
Route::view('message', config('olaindex.theme').'message')->name('message');
// 图床
Route::get('image', 'ManageController@uploadImage')->name('image')
    ->middleware('checkImage');
Route::post('image/upload', 'ManageController@uploadImage')
    ->name('image.upload')->middleware('throttle:10,2', 'checkImage');
Route::get('file/delete/{sign}', 'ManageController@deleteItem')->name('delete');
// 后台设置管理
Route::any('login', 'AdminController@login')->name('login');
Route::post('logout', 'AdminController@logout')->name('logout');

Route::prefix('admin')->group(function () {
    // 基础设置
    Route::any('/', 'AdminController@basic')->name('admin.basic');
    Route::any('bind', 'AdminController@bind')->name('admin.bind');
    Route::any('show', 'AdminController@show')->name('admin.show');
    Route::any('profile', 'AdminController@profile')->name('admin.profile');
    Route::any('clear', 'AdminController@clear')->name('admin.cache.clear');
    Route::any('refresh', 'AdminController@refresh')
        ->name('admin.cache.refresh');
    // 文件夹操作
    Route::prefix('folder')->group(function () {
        Route::post('lock', 'ManageController@lockFolder')->name('admin.lock');
        Route::post('create', 'ManageController@createFolder')
            ->name('admin.folder.create');
    });
    // 文件操作
    Route::prefix('file')->group(function () {
        Route::get('/', 'ManageController@uploadFile')->name('admin.file');
        Route::post('upload', 'ManageController@uploadFile')
            ->name('admin.file.upload')->middleware('throttle:10,2');
        Route::any('add', 'ManageController@createFile')
            ->name('admin.file.create');
        Route::any('edit/{id}', 'ManageController@updateFile')
            ->name('admin.file.update');
        Route::view('other', config('olaindex.theme').'admin.other')
            ->name('admin.other');
        Route::post('copy', 'ManageController@copyItem')->name('admin.copy');
        Route::post('move', 'ManageController@moveItem')->name('admin.move');
        Route::post('file/path2id', 'ManageController@pathToItemId')
            ->name('admin.path2id');
        Route::post('share', 'ManageController@createShareLink')
            ->name('admin.share');
        Route::post('share/delete', 'ManageController@deleteShareLink')
            ->name('admin.share.delete');
    });
    // 离线上传
    Route::post('url/upload', 'ManageController@uploadUrl')
        ->name('admin.url.upload');
});
// 搜索
Route::any('search', 'IndexController@search')->name('search')
    ->middleware('checkAuth', 'throttle:10,2');
Route::any('search/file/{id}', 'IndexController@searchShow')
    ->name('search.show')->middleware('checkAuth');

Route::get('about', function () {
    $url
        = 'https://raw.githubusercontent.com/WangNingkai/OLAINDEX/master/README.md';
    $content = \App\Helpers\Tool::getFileContent($url);
    $markdown = \App\Helpers\Tool::markdown2Html($content);

    return response()->view(config('olaindex.theme').'about',
        compact('markdown'));
});
