<?php

use App\Http\Controllers\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

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

// Route for users
Route::post('/register' , [ApiAuthController::class , 'register']);
Route::post('/login' , [ApiAuthController::class , 'login']);

// Protected Route
Route::middleware('auth:api')->group(function () {
    Route::resource('jobs', JobController::class);

});