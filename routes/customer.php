<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerAddressController;
use App\Http\Controllers\Customer\CustomerSettingController;

Route::group(['prefix' => 'customers', 'middleware' => ['auth']], function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::get('', 'index');
        Route::get('{id}/show', 'show');
        Route::post('store', 'store');
        Route::put('{id}/update', 'update');
        Route::delete('{id}/destroy', 'destroy');
    });
    Route::controller(CustomerAddressController::class)->group(function () {
        Route::get('{id}/addresses/show', 'show');
        Route::post('{id}/addresses/store', 'store');
        Route::put('{id}/addresses/update', 'update');
        Route::delete('{id}/addresses/destroy', 'destroy');
    });
    Route::controller(CustomerSettingController::class)->group(function () {
        Route::post('{id}/settings/store', 'store');
        Route::put('{id}/settings/update', 'update');
        Route::delete('{id}/settings/destroy', 'destroy');
    });
});