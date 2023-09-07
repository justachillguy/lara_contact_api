<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavourtieController;
use App\Http\Controllers\SearchRecordController;
use App\Models\SearchRecord;
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
            Route::get("contact-bin","bin")->name("contact.bin");
            Route::post("contact/restore/{id}", "restore")->name("contact.restore");
            Route::delete("contact/force-delete/{id}", "forceDelete")->name("contact.forceDelete");
            Route::post("contact/restore-all", "restoreAll")->name("contact.restore-all");
            Route::delete("contact/empty-bin", "emptyBin")->name("contact.emptyBin");
            Route::post("contact/multiple-delete", "multipleDelete")->name("multiple.delete");
        });

        Route::controller(FavourtieController::class)->group(function () {
            Route::get("favourite", "index")->name("fav.index");
            Route::post("favourite/{id}", "store")->name("fav.store");
            Route::delete("favourite/{id}", "destroy")->name("fav.destroy");
        });

        Route::controller(SearchRecordController::class)->group(function () {
            Route::get("search-record", "index")->name("searchRecord.index");
            Route::delete("search-record/{id}", "destroy")->name("searchRecord.destroy");
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
