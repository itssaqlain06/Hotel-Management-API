<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::middleware('api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',[AuthController::class,'user']);
});


Route::post('/register',[AuthController::class,'register']);

Route::post('/login', [AuthController::class, 'login']);

