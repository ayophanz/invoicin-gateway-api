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


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::get('refresh', 'refresh');
    Route::get('me', 'me');
});

Route::controller(AccountController::class)->group(function () {
    Route::post('account/store', 'store');
    Route::get('account/generate-2fa-qr-code', 'generateTwofaQRcode');
    Route::post('account/store-2fa-secret', 'storeTwofaSecret');
});

Route::fallback(function () {
    return response()->json(['Error' => 'Not Found'], Response::HTTP_NOT_FOUND);
});