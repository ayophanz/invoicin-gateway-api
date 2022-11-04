<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;

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
    return view('welcome');
});

Route::controller(AuthController::class)->group(function () {
    /** 
     * Password reset
     */
    Route::get('password-reset-link/{token}', 'passwordResetLink');
    Route::post('password-reset-link/{token}', 'resetPassword');
});

Route::controller(AccountController::class)->group(function () {
    /**
     * User verification
     */
    Route::get('verify-user/{token}', 'verifyUserLink');
    Route::post('verify-user/{token}', 'verifyUser');
    
    /**
     * Organization verification
     */
    Route::get('verify-organization/{token}', 'verifyOrganizationLink');
    Route::post('verify-organization/{token}', 'verifyOrganization');
});