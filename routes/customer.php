<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\CustomerController;

Route::group(['middleware' => ['auth']], function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customers', 'index');
        Route::get('customers/show', 'show');
        Route::post('customers/store', 'store');
        Route::put('customers/update/{id}', 'update');
        Route::delete('customers/destroy/{id}', 'destroy');
    });
});