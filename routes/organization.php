<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organization\OrganizationController;

Route::group(['prefix' => 'organization', 'middleware' => ['auth']], function () {
    Route::controller(OrganizationController::class)->group(function () {
        /** Index */
        Route::get('', 'index');
        Route::get('show', 'show');
    });
});