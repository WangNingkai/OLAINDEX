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

Route::get('/', 'GraphFetchController@oneFetchItemList');
Route::get('/menu/{path?}', 'GraphFetchController@oneFetchItemList')->name('list');
Route::get('/item/{itemId}', 'GraphFetchController@oneShowItem')->name('item');
Route::get('/item/{itemId}/download', 'GraphFetchController@oneFetchDownload')->name('download');
Route::get('/item/{itemId}/content', 'GraphFetchController@oneFetchContent')->name('content');
Route::get('/item/{itemId}/thumb', 'GraphFetchController@oneFetchThumb')->name('thumb')->middleware('throttle:10,10');
Route::get('/item/{itemId}/view', 'GraphFetchController@oneFetchView')->name('view')->middleware('throttle:10,10');

Route::get('/image', 'GraphPostController@uploadImage')->name('image');
Route::post('/image/upload', 'GraphPostController@uploadImage')->name('image.upload')->middleware('throttle:10,5');

Route::post('/item/delete', 'GraphPostController@deleteItem')->name('delete');

Route::any('/login', 'ManageController@login')->name('login');
Route::any('/admin', 'ManageController@basic')->name('admin.basic');
Route::any('/admin/show', 'ManageController@show')->name('admin.show');
Route::any('/admin/profile', 'ManageController@profile')->name('admin.profile');
Route::any('/admin/clear', 'ManageController@clear')->name('admin.clear');
Route::post('/logout', 'ManageController@logout')->name('logout');





