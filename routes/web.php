<?php

use Illuminate\Support\Facades\Route;


Route::name('statamic.')->group(function () {
    Route::group(['prefix' => config('statamic.routes.action'), 'namespace' => 'AltDesign\AltPasswordProtect\Http\Controllers'], function () {
        Route::post('protect/password', [\AltDesign\AltPasswordProtect\Http\Controllers\AltController::class, 'store'])->name('protect.password.store');
    });
});

