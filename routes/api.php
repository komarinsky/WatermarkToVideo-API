<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\VideoFileController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('videos')->group(function () {
    Route::controller(VideoFileController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{video}', 'show');
    });
});
