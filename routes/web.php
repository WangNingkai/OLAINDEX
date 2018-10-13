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

// 安装
Route::any('/install', 'InitController@_1stInstall')->name('_1stInstall');
Route::any('/install/apply', 'InitController@apply')->name('apply');

// 授权、刷新Token
Route::get('/oauth', 'OauthController@oauth')->name('oauth');
Route::get('/refresh', 'OauthController@refreshToken')->name('refresh');

// 目录显示
Route::get('/', 'FetchController@oneFetchItemList');
Route::get('/list/{path?}', 'FetchController@oneFetchItemList')->name('list');
Route::get('/item/{itemId}', 'FetchController@oneShowItem')->name('item');
Route::get('/item/download/{itemId}', 'FetchController@oneFetchDownload')->name('download');
Route::get('/item/content/{itemId}', 'FetchController@oneFetchContent')->name('content');
Route::get('/item/thumb/{itemId}', 'FetchController@oneFetchThumb')->name('thumb')->middleware('throttle:10,2');
Route::get('/item/view/{itemId}', 'FetchController@oneFetchView')->name('view')->middleware('throttle:10,2');
Route::get('/item/origin/view/{itemId}', 'FetchController@oneFetchDownload')->name('origin.view');
Route::post('/password', 'FetchController@oneHandlePassword')->name('password');

// 图床
Route::get('/image', 'ManageController@uploadImage')->name('image')->middleware('checkImage');
Route::post('/image/upload', 'ManageController@uploadImage')->name('image.upload')->middleware('throttle:10,2','checkImage');
Route::get('/item/delete/{itemId}', 'ManageController@deleteItem')->name('delete')->middleware('checkImage');

// 后台小文件上传
Route::get('/admin/file', 'ManageController@uploadFile')->name('admin.file')->middleware('checkAuth');
Route::post('/admin/file/upload', 'ManageController@uploadFile')->name('admin.file.upload')->middleware('checkAuth','throttle:10,2');

// 后台管理
Route::any('/login', 'AdminController@login')->name('login');
Route::any('/admin', 'AdminController@basic')->name('admin.basic');
Route::any('/admin/show', 'AdminController@show')->name('admin.show');
Route::any('/admin/profile', 'AdminController@profile')->name('admin.profile');
Route::any('/admin/clear', 'AdminController@clear')->name('admin.clear');
Route::post('/logout', 'AdminController@logout')->name('logout');
