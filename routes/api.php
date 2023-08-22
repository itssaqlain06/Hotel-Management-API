<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('user/register',[AuthController::class,'register']);
Route::post('user/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function(){
    // User routes
    Route::post('user/logout', [AuthController::class, 'logout']);
    Route::get('user',[AuthController::class,'show']);
    Route::get('user/{user}',[AuthController::class,'index']);
    Route::delete('user/delete/{user}',[AuthController::class,'destroy']);

    // Hotel routes
    Route::post('hotel/store',[HotelController::class,'store']);
    Route::get('hotel',[HotelController::class,'show']);
    Route::get('hotel/{hotel}',[HotelController::class,'index']);
    Route::delete('hotel/delete/{hotel}',[HotelController::class,'destroy']);
    Route::put('hotel/update/{hotel}',[HotelController::class,'update']);

    // Room routes
    Route::post('room/store',[RoomController::class,'store']);
    Route::get('room',[RoomController::class,'show']);
    Route::get('room/{room}',[RoomController::class,'index']);
    Route::delete('room/delete/{room}',[RoomController::class,'destroy']);
    Route::put('room/update/{room}',[RoomController::class,'update']);
});
