<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CustomerController;

Route::group(['prefix' => 'customers', 'middleware' => ['auth']], function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::get('', 'index');
        Route::get('show', 'show');
        Route::post('store', 'store');
    });
});