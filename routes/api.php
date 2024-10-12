<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
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
Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::group([
        'middleware' => ['auth_user:api'],
    ], function () {
        Route::get('logout', [AuthController::class, 'logout']);
    });
});
Route::group([
    'middleware' => ['auth_user']
], function () {
    Route::post('store', [FileController::class, 'store']);//upload file
    Route::post('checkin/{id}', [FileController::class, 'chechIn']);//checkin method
    Route::get('/downloadFile/{id}', [FileController::class, 'downloadFile']);
    Route::post('/upFile', [FileController::class, 'uploadFile']);//upload file again
    Route::post('checkout/{id}', [FileController::class, 'checkout']);//checkout method
    Route::post('uploadFiles', [FileController::class, 'uploadFiles']);//multi files upload
    Route::post('group', [GroupController::class, 'createGroup']);//create group
    Route::get('users', [AuthController::class, 'users']);//show all users
    Route::get('usersGroup', [GroupController::class, 'usersGroup']);
    Route::get('filesGroup/{id}', [GroupController::class, 'filesGroup']);
    Route::delete('deleteAccount', [AuthController::class, 'deleteAccount']);
    Route::post('addUserToGroup/{id}', [GroupController::class, 'addUserToGroup']);
    Route::get('usersGroup/{id}', [AuthController::class, 'usersGroup']);
});





