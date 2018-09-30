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

Route::get('/oauth', 'OauthController@oauth')->name('oauth');
Route::get('/refresh', 'OauthController@refreshToken')->name('refresh');

Route::get('/', 'GraphController@oneFetchItemList');
Route::get('/menu/{path?}', 'GraphController@oneFetchItemList')->name('list');
Route::get('/item/{itemId}', 'GraphController@oneShowItem')->name('item');
Route::get('/item/{itemId}/download', 'GraphController@oneFetchDownload')->name('download');
Route::get('/item/{itemId}/thumb', 'GraphController@oneFetchThumb')->name('thumb');
Route::get('/item/{itemId}/content', 'GraphController@oneFetchContent')->name('content');
Route::get('/item/{itemId}/view', 'GraphController@oneFetchView')->name('view');

Route::any('/login', 'ManageController@login')->name('login');
Route::any('/admin', 'ManageController@basic')->name('admin.basic');
Route::any('/admin/show', 'ManageController@show')->name('admin.show');
Route::any('/admin/profile', 'ManageController@profile')->name('admin.profile');
Route::any('/admin/clear', 'ManageController@clear')->name('admin.clear');
Route::post('/logout', 'ManageController@logout')->name('logout');





