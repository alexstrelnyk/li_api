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

Route::get('/open-app/{token}', 'BaseController@openApp')->name('open-app');
Route::get('/call-command/{command}', 'BaseController@callCommand')->name('call-command');
Route::get('/phpinfo', 'BaseController@phpinfo')->name('phpinfo');
