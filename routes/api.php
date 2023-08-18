<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',[AuthController::class,'user']);
    Route::post('/hotel',[HotelController::class,'store']);
});


Route::post('/register',[AuthController::class,'register']);

Route::post('/login', [AuthController::class, 'login']);

