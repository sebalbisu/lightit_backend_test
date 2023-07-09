<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TestHelpers\TestController;
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

Route::post('/register', [RegisterController::class, 'register'])->name('register');

// helpers for api testing
Route::get('/test-helper/user/{user}', [TestController::class, 'getUser']);
Route::delete('/test-helper/user', [TestController::class, 'deleteAllUsers']);
Route::get('/test-helper/user', [TestController::class, 'allUsers']);
Route::delete('/test-helper/user/{user}', [TestController::class, 'deleteUser']);
