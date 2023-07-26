<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ContactController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix("v1")->group(function () {

    Route::middleware("auth:sanctum")->group(function () {
        Route::apiResource('contact', ContactController::class);
        Route::controller(ContactController::class)->group(function () {
            Route::get("contact-trash", "trash")->name("contact.trash");
            Route::post("contact-restore/{id}", "restore")->name("contact.restore");
            Route::delete("contact-force-delete/{id}", "forceDelete")->name("contact.forceDelete");
        });

        Route::controller(ApiAuthController::class)->group(function () {
            Route::post("logout", 'logout');
            Route::post("logout-all", 'logoutAll');
            Route::get("devices", 'devices');
        });
    });

    Route::controller(ApiAuthController::class)->group(function () {
        Route::post("register", 'register');
        Route::post("login", 'login');
    });
});
