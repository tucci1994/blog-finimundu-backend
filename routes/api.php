<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
     ->middleware([ForceJsonResponse::class])
     ->group(function () {

         Route::prefix('auth')->name('auth.')->group(function () {
             Route::post('login', [AuthController::class, 'login'])->name('login');

             Route::middleware('auth:sanctum')->group(function () {
                 Route::post('logout', [AuthController::class, 'logout'])->name('logout');
                 Route::get('me',      [AuthController::class, 'me'])->name('me');
             });
         });

         Route::middleware('auth:sanctum')
              ->apiResource('posts', PostController::class);
     });
