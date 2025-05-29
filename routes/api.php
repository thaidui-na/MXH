<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/users/search', [UserController::class, 'search']);
}); 

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
}); 

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/theme', function (Request $request) {
        // Implementation of the theme route
    });
}); 