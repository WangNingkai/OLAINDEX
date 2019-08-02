<?php

use Illuminate\Support\Str;

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
Route::group(['middleware' => 'detectOneDrive'], function () {
    Route::any('/oauth/onedrive/{onedrive}', 'OauthController@oauth')->name('oauth');
    Route::any('/callback/onedrive/{onedrive}', 'OauthController@callback')->name('callback');
});

// 前台
Route::group(['namespace' => 'Index'], function () {
    Route::get('/', 'Auth\\LoginController@showLoginForm')->name('login');
    Route::post('/', 'Auth\\LoginController@login');

    Route::group(['middleware' => ['auth:web']], function () {
        Route::post('/logout', 'Auth\\LoginController@logout')->name('logout');
        Route::get('onedrive', 'OneDriveController@index')->name('onedrive.list');
        Route::group(['middleware' => [
            'detectOneDrive',
            'checkOneDrive',
            'checkToken',
            'handleIllegalFile',
            'handleEncryptDir'
        ], 'prefix' => 'onedrive/{onedrive}'], function () {
            Route::get('home/{query?}', 'IndexController@list')->where('query', '.*')->name('home');
            Route::get('show/{query}', 'IndexController@show')->where('query', '.*')->name('show');
            Route::group(['middleware' => ['hotlinkProtection']], function () {
                Route::get('down/{query}', 'IndexController@download')->where('query', '.*')->name('download');
                Route::get('view/{query}', 'IndexController@view')->where('query', '.*')->name('view');
            });

            Route::post('password', 'IndexController@handlePassword')->name('password');
            Route::get('thumb/{id}/size/{size}', 'IndexController@thumb')->name('thumb');
            Route::get('thumb/{id}/{width}/{height}', 'IndexController@thumbCrop')->name('thumb_crop');

            // 搜索
            Route::get('search', 'IndexController@search')->name('search')->middleware('throttle:10,2');
            Route::get('search/file/{id}', 'IndexController@searchShow')->name('search.show');
        });
    });
});

Route::group([
    'middleware' => [
        'auth:web',
        'detectOneDrive',
        'checkOneDrive',
        'checkToken',
        'handleIllegalFile',
        'handleEncryptDir'
    ],
    'prefix' => 'onedrive/{onedrive}'
], function () {
    // 图床
    Route::get('image', 'Admin\\ManageController@uploadImage')->name('image')->middleware('checkImage');
    Route::post('image/upload', 'Admin\\ManageController@uploadImage')->name('image.upload')->middleware('throttle:10,2', 'checkImage');
    Route::get('file/delete/{sign}', 'Admin\\ManageController@deleteItem')->name('delete');
});

Route::view('message', config('olaindex.theme') . 'message')->name('message');

// 后台设置管理
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', 'Admin\\AuthController@showLoginForm')->name('admin.login');
    Route::post('login', 'Admin\\AuthController@login');

    Route::group(['middleware' => 'auth:admin', 'namespace' => 'Admin'], function () {
        Route::post('logout', 'AuthController@logout')->name('admin.logout');
        Route::get('aria2c', 'UtilController@aria2c')->name('admin.aria2c');
        Route::view('show', config('olaindex.theme') . 'admin.show')->name('admin.show');
        Route::view('profile', config('olaindex.theme') . 'admin.profile')->name('admin.profile.show');
        Route::post('image', 'UtilController@storeImage')->name('admin.image');
        Route::post('image/delete', 'UtilController@destroyImage')->name('admin.image.delete');
        Route::get('onedrive_list', 'UtilController@list')->name('admin.onedrive.list');

        // 基础设置
        Route::get('/', 'AdminController@showBasic')->name('admin.basic');
        Route::post('/', 'AdminController@basic')->name('admin.basic.post');
        Route::post('profile', 'AdminController@profile')->name('admin.profile.post');

        // onedrive
        Route::resource('onedrive', 'OneDriveController', ['as' => 'admin', 'except' => 'show']);
        Route::group(['prefix' => 'onedrive/{onedrive}'], function () {
            Route::get('bind', 'OneDriveController@showBind')->name('admin.onedrive.showBind');
            Route::post('bind', 'OneDriveController@bind')->name('admin.onedrive.bind');
            Route::post('unbind', 'OneDriveController@unbind')->name('admin.onedrive.unbind');
            Route::post('apply', 'OneDriveController@apply')->name('admin.onedrive.apply');
            Route::group(['middleware' => 'detectOneDrive'], function () {
                Route::any('clear', 'OneDriveController@clear')->name('admin.onedrive.clear');
                Route::any('refresh', 'OneDriveController@refresh')->name('admin.onedrive.refresh');
                Route::group(['middleware' => 'checkToken'], function () {
                    // 文件夹操作
                    Route::group(['prefix' => 'folder'], function () {
                        Route::post('lock', 'ManageController@lockFolder')->name('admin.onedrive.lock');
                        Route::post('create', 'ManageController@createFolder')->name('admin.onedrive.folder.create');
                    });

                    // 文件操作
                    Route::group(['prefix' => 'file'], function () {
                        Route::get('/', 'ManageController@showFile')->name('admin.onedrive.file');
                        Route::post('upload', 'ManageController@uploadFile')->name('admin.onedrive.file.upload')->middleware('throttle:10,2');
                        Route::any('add', 'ManageController@createFile')->name('admin.onedrive.file.create');
                        Route::any('edit/{id}', 'ManageController@updateFile')->name('admin.onedrive.file.update');
                        Route::view('other', config('olaindex.theme') . 'admin.other')->name('admin.onedrive.other');
                        Route::post('copy', 'ManageController@copyItem')->name('admin.onedrive.copy');
                        Route::post('move', 'ManageController@moveItem')->name('admin.onedrive.move');
                        Route::post('file/path2id', 'ManageController@pathToItemId')->name('admin.onedrive.path2id');
                        Route::post('share', 'ManageController@createShareLink')->name('admin.onedrive.share');
                        Route::post('share/delete', 'ManageController@deleteShareLink')->name('admin.onedrive.share.delete');
                    });

                    // 离线上传
                    Route::post('url/upload', 'ManageController@uploadUrl')->name('admin.onedrive.url.upload');
                });
            });
        });
    });
});

if (Str::contains(config('app.url'), ['localhost', 'dev.ningkai.wang'])) {
    Route::get('about', function () {
        $url = 'https://raw.githubusercontent.com/WangNingkai/OLAINDEX/master/README.md';
        $content = getFileContent($url);
        $markdown = markdown2Html($content);

        return response()->view(
            config('olaindex.theme') . 'about',
            compact('markdown')
        );
    });
}
