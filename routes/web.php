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
// 索引
Route::get('/', 'IndexController@home');
Route::get('/home/{query?}', 'IndexController@list')->where('query', '.*')->name('home');
Route::get('/show/{query}', 'IndexController@show')->where('query', '.*')->name('show');
Route::get('/download/{query}', 'IndexController@download')->where('query', '.*')->name('download')->middleware('hotlinkProtection');;
Route::get('/view/{query}', 'IndexController@view')->where('query', '.*')->name('view')->middleware('hotlinkProtection');;
Route::post('/password', 'IndexController@handlePassword')->name('password');
// 图床
Route::get('/image', 'ManageController@uploadImage')->name('image')->middleware('checkImage');
Route::post('/image/upload', 'ManageController@uploadImage')->name('image.upload')->middleware('throttle:10,2', 'checkImage');
Route::get('/item/delete/{sign}', 'ManageController@deleteItem')->name('delete');

// 管理
Route::any('/login', 'AdminController@login')->name('login');
Route::post('/logout', 'AdminController@logout')->name('logout');
Route::any('/admin', 'AdminController@basic')->name('admin.basic');
Route::any('/admin/show', 'AdminController@show')->name('admin.show');
Route::any('/admin/profile', 'AdminController@profile')->name('admin.profile');
Route::any('/admin/clear', 'AdminController@clear')->name('admin.clear');

// 文件上传
Route::get('/admin/file', 'ManageController@uploadFile')->name('admin.file');
Route::post('/admin/file/upload', 'ManageController@uploadFile')->name('admin.file.upload')->middleware('throttle:10,2');
Route::post('/admin/lock/folder', 'ManageController@lockFolder')->name('lock');
// 文件管理
Route::any('/admin/file/add', 'ManageController@createFile')->name('file.create');
Route::any('/admin/file/edit/{id}', 'ManageController@updateFile')->name('file.update');
Route::post('/admin/folder/create', 'ManageController@createFolder')->name('folder.create');
// 搜索
Route::any('/search', 'IndexController@search')->name('search')->middleware('checkAuth');
