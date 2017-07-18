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


Route::any('/', 'IndexController@index')->name('index');
Route::any('tourists', 'IndexController@tourists')->name('tourists');
Route::any('losspassword', 'IndexController@losspassword')->name('losspassword');
Route::post('login', 'IndexController@login')->name('login');
Route::any('register', 'IndexController@register')->name('register');
Route::any('captcha/{tmp}', 'IndexController@captcha')->name('captcha');
Route::any('logout', 'IndexController@logout')->name('logout');
Route::any('certificate', 'IndexController@certificate')->name('certificate');
Route::any('perfectinformation', 'IndexController@perfectinformation')->name('perfectinformation');
Route::any('addemail', 'IndexController@addemail')->name('addemail');
Route::post('setemail', 'IndexController@setemail')->name('setemail');
Route::post('adduser', 'IndexController@adduser')->name('adduser');
Route::any('active/{code}', 'IndexController@active')->name('active');
Route::post('addapply', 'IndexController@addapply')->name('addapply');
Route::post('addinformation/{id}', 'IndexController@addinformation')->where('id', '[0-9]+')->name('addinformation');
Route::post('sendemail', 'IndexController@sendemail')->name('sendemail');
Route::any('setpassword/{code}', 'IndexController@setpassword')->name('setpassword');
Route::post('changepassword', 'IndexController@changepassword')->name('changepassword');

Route::any('backstage', 'BackstageController@index')->name('backstage_index');
Route::any('backstage/applypass/{id}', 'BackstageController@applypass')->where('id', '[0-9]+')->name('backstage_applypass');
Route::post('backstage/applydispass/{id}', 'BackstageController@applydispass')->where('id', '[0-9]+')->name('backstage_applydispass');
Route::any('backstage/logout', 'BackstageController@logout')->name('backstage_logout');
Route::any('backstage/approval', 'BackstageController@approval')->name('backstage_approval');

Route::any('home', 'HomeController@index')->name('home_index');