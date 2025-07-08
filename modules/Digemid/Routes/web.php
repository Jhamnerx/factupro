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


use Illuminate\Support\Facades\Route;

$current_hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);

if ($current_hostname) {
    Route::domain($current_hostname->fqdn)->group(function () {
        Route::middleware(['auth', 'locked.tenant'])->group(function () {
            Route::prefix('digemid')->group(function () {
                Route::get('/', 'DigemidController@index')->name('tenant.digemid.index');
                Route::post('/update_exportable/{item?}', 'DigemidController@updateExportableItem');

                // Rutas para DIGEMID Ayuda
                Route::prefix('ayuda')->group(function () {
                    Route::get('/', 'DigemidAyudaController@index')->name('tenant.digemid.ayuda.index');
                    Route::get('/import', 'DigemidAyudaController@import')->name('tenant.digemid.ayuda.import');
                    Route::post('/process_import', 'DigemidAyudaController@processImport')->name('tenant.digemid.ayuda.process_import');
                    Route::get('/list', 'DigemidAyudaController@list')->name('tenant.digemid.ayuda.list');
                    Route::get('/search-by-reg-san/{regSan}', 'DigemidAyudaController@searchByRegSan')->name('tenant.digemid.ayuda.search_by_reg_san');
                });
            });
        });
    });
}
