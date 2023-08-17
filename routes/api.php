<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register',[AuthController::class,'store']);

/* Route::post('/login', function (Request $request) {
    $credentials = $request->only(['email', 'password']);

    if (!$token = auth('api')->attempt($credentials)) {
        abort(401, 'Nao Autorizado');
    }

    return response()->json([
        'data' => [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]
    ]);
 });*/


 Route::post('/login', [AuthController::class, 'geToken']);
