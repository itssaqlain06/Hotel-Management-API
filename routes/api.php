<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);
Route::get('user/details', [AuthController::class, 'userDetails']);
Route::get('user/booking', [AuthController::class, 'booking']);

Route::middleware('auth:api')->group(function () {
    // User routes
    Route::get('user', [AuthController::class, 'show']);
    Route::get('user/{user}', [AuthController::class, 'index']);
    Route::post('user/logout', [AuthController::class, 'logout']);
    Route::delete('user/delete/{user}', [AuthController::class, 'destroy']);

    // Hotel routes
    Route::post('hotel/store', [HotelController::class, 'store']);
    Route::get('hotel', [HotelController::class, 'show']);
    Route::get('hotel/{hotel}', [HotelController::class, 'index']);
    Route::put('hotel/update/{hotel}', [HotelController::class, 'update']);
    Route::delete('hotel/delete/{hotel}', [HotelController::class, 'destroy']);

    // Room routes
    Route::post('room/store', [RoomController::class, 'store']);

    Route::put('room/update/{room}', [RoomController::class, 'update']);
    Route::delete('room/delete/{room}', [RoomController::class, 'destroy']);

    // Booking routes
    Route::post('booking/store', [BookingController::class, 'store']);
    Route::get('booking', [BookingController::class, 'show']);
    Route::get('booking/{booking}', [BookingController::class, 'index']);
    Route::delete('booking/delete/{booking}', [BookingController::class, 'destroy']);
    Route::put('booking/update/{booking}', [BookingController::class, 'update']);
    Route::post('booking/cancel/{booking}', [BookingController::class, 'cancel']);

    // Reservation routes
    Route::post('reservation/store', [ReservationController::class, 'store']);
    Route::get('reservation', [ReservationController::class, 'show']);
    Route::get('reservation/{reservation}', [ReservationController::class, 'index']);
    Route::put('reservation/update/{reservation}', [ReservationController::class, 'update']);
    Route::delete('reservation/delete/{reservation}', [ReservationController::class, 'destroy']);
    Route::post('reservation/cancel/{reservation}', [ReservationController::class, 'cancel']);
    Route::post('reservation/confirm/{reservation}', [ReservationController::class, 'confirm']);
});

// Room route without JWT
Route::get('room', [RoomController::class, 'show']);
Route::get('room/{room}', [RoomController::class, 'index']);
