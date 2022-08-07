<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Organization\OrganizationAddressController;

Route::group(['prefix' => 'organization', 'middleware' => ['auth']], function () {
    Route::controller(OrganizationController::class)->group(function () {
        Route::get('', 'index');
        Route::get('show', 'show');
    });
    Route::controller(OrganizationAddressController::class)->group(function () {
        Route::post('addresses/store', 'store');
        Route::put('addresses/update', 'update');
    });
});