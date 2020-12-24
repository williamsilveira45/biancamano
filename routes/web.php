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

Route::get('/', function () {
    return redirect('login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia\Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('/customers')->name('customers.')->group(function () {
        Route::get('/', 'CustomerController@show')->name('index');
        Route::get('/json', 'CustomerController@json')->name('json');
        Route::get('/create', 'CustomerController@create')->name('create');
        Route::post('/store', 'CustomerController@store')->name('store');
        Route::delete('/{customer}', 'CustomerController@delete')->name('delete');
        Route::put('/{customer}', 'CustomerController@update')->name('update');
        Route::post('/active/{customer}', 'CustomerController@active')->name('active');
    });

    Route::prefix('/files')->name('files.')->group(function () {
        Route::get('/', 'FileController@show')->name('index');
        Route::get('/json', 'FileController@json')->name('json');
        Route::post('/upload', 'FileController@upload')->name('upload');
        Route::delete('/{customer}', 'FileController@delete')->name('delete');
        Route::put('/{customer}', 'FileController@update')->name('update');
    });
});
