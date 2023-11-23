<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['statamic.cp.authenticated'], 'namespace' => 'AltDesign\AltPasswordProtect\Http\Controllers'], function() {
    // Settings
    Route::get('/alt-design/alt-password-protect/', 'AltController@index')->name('alt-password-protect.index');
    Route::post('/alt-design/alt-password-protect/', 'AltController@update')->name('alt-password-protect.update');
});
