<?php

use App\Http\Controllers\RootCertController;

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

Route::get('/', function () {
    return view('home');
});

Route::get('/rootcert', function () {
    return view('home');
});
Route::post('/rootcert/load', 'RootCertController@load');
Route::post('/rootcert', 'RootCertController@push');

Route::get('/csr', function () {
    return view('home');
});
Route::post('/csr', 'NewCertController@create');

Route::get('/activate', function () {
    return view('home');
});
Route::post('/activate/list', 'ActivationController@fetch');
Route::post('/activate', 'ActivationController@activate');


