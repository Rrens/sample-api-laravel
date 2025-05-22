<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MailsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {

    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::group([
        'prefix' => 'mail',
    ], function () {
        Route::get('', [MailsController::class, 'index']);
        Route::post('sent_message/{user_id}', [MailsController::class, 'sent_message']);
    });
    // ])
});

Route::middleware('auth:sanctum')->get('/check-auth', function (Request $request) {
    dd(auth()->user());
});

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('tes', function () {
        dd(auth()->user());
    });
});
