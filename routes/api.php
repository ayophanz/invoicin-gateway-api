<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// test middleware
Route::get('test-middleware', function () {
    return "2FA middleware work!";
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::get('refresh', 'refresh');
    Route::post('verify-otp', 'verifyOtp')->middleware([ 'throttle:5' ]);
    Route::post('generate-secret', 'generate2faSecret');
    Route::post('generate-2fa-qr-code', 'generateTwofaQRcode');
    Route::post('enable-2fa', 'enable2fa');
    
    Route::middleware(['auth:api'])->group(function() {
        Route::post('disable-2fa', 'disable2fa');
        Route::get('me', 'me');
    });
});

Route::controller(AccountController::class)->group(function () {
    Route::post('account/store', 'store');
});

Route::fallback(function () {
    return response()->json(['Error' => 'Not Found'], Response::HTTP_NOT_FOUND);
});