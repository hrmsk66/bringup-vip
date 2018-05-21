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

Route::get('/controllers', function () {
    return view('home');
});
Route::post('/controllers/load', 'Controllers@load');
Route::post('/controllers/push', 'Controllers@push');
Route::post('/controllers/resync', 'Controllers@resync');
Route::post('/controllers/csr', 'Controllers@csr');

Route::get('/vedge', function () {
    return view('home');
});
Route::post('/vedge/load', 'vEdge@load');
Route::post('/vedge/push', 'vEdge@push');
Route::post('/vedge/list', 'vEdge@fetch');
Route::post('/vedge/activate', 'vEdge@activate');