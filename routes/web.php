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
Route::group(['middleware' => 'checkToken'], function() {
    Route::get('/', function(){
        return redirect()->route('dir');
    });
    Route::get('/home/{path?}', 'FetchController@fetchMenu')->name('dir');
    Route::get('/home/{path?}/{fileName?}', 'FetchController@downloadItem')->name('file');
    Route::get('/home/show/{path?}/{fileName?}', 'FetchController@fetchItem')->name('show');
    Route::get('/dev/{path?}', 'GraphController@testFetchItemList')->name('list');
    Route::get('/dev/item/{itemId?}', 'GraphController@testFetchItem')->name('item');
    Route::get('/dev/item/{itemId?}/thumb', 'GraphController@testFetchThumb')->name('thumb');
    Route::get('/dev/item/{itemId?}/content', 'GraphController@testFetchContent')->name('content');
});
Route::any('/login', 'ManageController@login')->name('login');
Route::group(['middleware' => 'checkAuth'], function() {
    Route::any('/admin', 'ManageController@basic')->name('admin.basic');
    Route::any('/admin/show', 'ManageController@show')->name('admin.show');
    Route::any('/admin/profile', 'ManageController@profile')->name('admin.profile');
    Route::any('/admin/clear', 'ManageController@clear')->name('admin.clear');
    Route::post('/logout', 'ManageController@logout')->name('logout');
});





