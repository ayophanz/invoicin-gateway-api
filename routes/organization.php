<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Organization\OrganizationAddressController;
use App\Http\Controllers\Organization\OrganizationSettingController;

Route::group(['prefix' => 'organization', 'middleware' => ['auth']], function () {
    Route::controller(OrganizationController::class)->group(function () {
        Route::get('show', 'show');
    });
    Route::controller(OrganizationAddressController::class)->group(function () {
        Route::post('addresses/store', 'store');
        Route::put('addresses/update', 'update');
        Route::delete('addresses/destroy', 'destroy');
    });
    Route::controller(OrganizationSettingController::class)->group(function () {
        Route::post('settings/store', 'store');
        Route::put('settings/update', 'update');
        Route::delete('settings/destroy', 'destroy');
    });
});