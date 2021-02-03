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

    /**
     * CUSTOMER
     */
    Route::prefix('/customers')->name('customers.')->group(function () {
        Route::get('/', 'CustomerController@show')->name('index');
        Route::get('/json', 'CustomerController@json')->name('json');
        Route::get('/create', 'CustomerController@create')->name('create');
        Route::post('/store', 'CustomerController@store')->name('store');
        Route::delete('/{customer}', 'CustomerController@delete')->name('delete');
        Route::put('/{customer}', 'CustomerController@update')->name('update');
        Route::post('/active/{customer}', 'CustomerController@active')->name('active');
        /**
         * CONFIG
         */
        Route::prefix('/{customer}/config')->name('config.')->group(function () {
            Route::get('/', 'CustomerConfigController@config')->name('index');
            /**
             * PLANO DE CONTAS CUSTOMER
             */
            Route::prefix('/contas')->name('contas.')->group(function () {
                Route::post('/store', 'CustomerContasController@store')->name('store');
                Route::post('/readfile', 'CustomerContasController@readfile')->name('readfile');
                Route::post('/regcontas', 'CustomerContasController@regcontas')->name('regcontas');
                Route::get('/jsoncontas', 'CustomerContasController@jsonContas')->name('jsonContas');
                Route::put('/{customerconta}', 'CustomerContasController@update')->name('update');
                Route::post('/active/{customerconta}', 'CustomerContasController@active')->name('active');
                Route::delete('/{customerconta}', 'CustomerContasController@delete')->name('delete');
            });
        });
    });

    /**
     * FILES
     */
    Route::prefix('/files')->name('files.')->group(function () {
        Route::get('/', 'FileController@show')->name('index');
        Route::get('/json', 'FileController@json')->name('json');
        Route::post('/upload', 'FileController@upload')->name('upload');
        Route::delete('/{file}', 'FileController@delete')->name('delete');
        Route::put('/{file}', 'FileController@update')->name('update');
    });

    Route::prefix('/sistema')->name('sistema.')->group(function () {
        Route::prefix('/pconta')->name('pconta.')->group(function() {
            Route::get('/', 'PlanoContasController@show')->name('index');
            Route::get('/json', 'PlanoContasController@json')->name('json');
            Route::get('/create', 'PlanoContasController@create')->name('create');
            Route::post('/store', 'PlanoContasController@store')->name('store');
            Route::delete('/{pconta}', 'PlanoContasController@delete')->name('delete');
            Route::put('/{pconta}', 'PlanoContasController@update')->name('update');
        });
    });
});
