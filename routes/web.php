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
Route::any('/1st-install', 'InitController@_1stInstall')->name('_1stInstall');
Route::any('/app-apply', 'InitController@apply')->name('apply');

// 授权、刷新Token
Route::get('/oauth', 'OauthController@oauth')->name('oauth');
Route::get('/refresh', 'OauthController@refreshToken')->name('refresh');

// 目录显示
Route::get('/', 'GraphFetchController@oneFetchItemList');
Route::get('/list/{path?}', 'GraphFetchController@oneFetchItemList')->name('list');
Route::get('/item/{itemId}', 'GraphFetchController@oneShowItem')->name('item');
Route::get('/item/download/{itemId}', 'GraphFetchController@oneFetchDownload')->name('download');
Route::get('/item/content/{itemId}', 'GraphFetchController@oneFetchContent')->name('content');
Route::get('/item/thumb/{itemId}', 'GraphFetchController@oneFetchThumb')->name('thumb')->middleware('throttle:10,2');
Route::get('/item/view/{itemId}', 'GraphFetchController@oneFetchView')->name('view')->middleware('throttle:10,2');
Route::get('/item/origin/view/{itemId}', 'GraphFetchController@oneFetchDownload')->name('origin.view');

// 图床
Route::get('/image', 'GraphPostController@uploadImage')->name('image')->middleware('checkImage');
Route::post('/image/upload', 'GraphPostController@uploadImage')->name('image.upload')->middleware('throttle:10,2','checkImage');
Route::get('/item/delete/{itemId}', 'GraphPostController@deleteItem')->name('delete')->middleware('checkImage');

// 后台小文件上传
Route::get('/admin/file', 'GraphPostController@uploadFile')->name('admin.file')->middleware('checkAuth');
Route::post('/admin/file/upload', 'GraphPostController@uploadFile')->name('admin.file.upload')->middleware('checkAuth','throttle:10,2');

// 后台管理
Route::any('/login', 'ManageController@login')->name('login');
Route::any('/admin', 'ManageController@basic')->name('admin.basic');
Route::any('/admin/show', 'ManageController@show')->name('admin.show');
Route::any('/admin/profile', 'ManageController@profile')->name('admin.profile');
Route::any('/admin/clear', 'ManageController@clear')->name('admin.clear');
Route::post('/logout', 'ManageController@logout')->name('logout');
