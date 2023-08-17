<?php

use App\Http\Controllers\Api\AuthController;
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

Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/login', function (Request $request) {
//     $credentials = $request->only(['email', 'password']);

//     if (!$token = auth('api')->attempt($credentials)) {
//         abort(401, 'Nao Autorizado');
//     }

//     return response()->json([
//         'data' => [
//             'token' => $token,
//             'token_type' => 'bearer',
//             'expires_in' => auth()->factory()->getTTL() * 60
//         ]
//     ]);
// });

Route::post('/login', [AuthController::class, 'geToken']);
