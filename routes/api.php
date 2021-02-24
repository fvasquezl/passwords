<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UserController;
use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
JsonApi::register('v1')->routes(function ($api) {
    $api->resource('entries')->relationships(function ($api) {
        $api->hasOne('authors');
        $api->hasOne('categories');
    });

    $api->resource('authors')->only('index', 'read')->relationships(function ($api) {
        $api->hasMany('entries')->except('replace', 'add', 'remove');
    });

    $api->resource('categories')->relationships(function ($api) {
        $api->hasMany('entries')->except('replace', 'add', 'remove');
    });

    Route::post('login', [LoginController::class, 'login'])->name('login')
        ->middleware('guest:sanctum');

    Route::post('logout', [LoginController::class, 'logout'])
        ->middleware('auth:sanctum')->name('logout');

    Route::post('register', [RegisterController::class, 'register'])
        ->middleware('guest:sanctum')
        ->name('register');

    Route::get('user', UserController::class)
        ->middleware('auth:sanctum')->name('user');

});


