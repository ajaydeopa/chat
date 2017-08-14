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
Route::get('/', function() {
    return redirect('login');
});

Route::get('/fire/{group}', 'HomeController@message');

/*Route::get('', function ($group) {
    event(new App\Events\EventName($group));
    return "event fired";
});*/

Route::get('test', function () {
    // this checks for the event
    return view('test');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/new/channel', 'HomeController@newChannel');
Route::post('/new/friend', 'HomeController@newFriend');
Route::post('/new/user', 'HomeController@newUser');
Route::post('/check/channel', 'HomeController@checkChannel');
Route::post('/check/member', 'HomeController@checkMember');
Route::post('/check/friend', 'HomeController@checkFriend');
Route::get('/get/messages', 'HomeController@channelMessages');
Route::post('/member/list', 'HomeController@channelMembers');
Route::post('/song/upload', 'HomeController@uploadSong');
Route::post('get/songs', 'HomeController@allSongs');