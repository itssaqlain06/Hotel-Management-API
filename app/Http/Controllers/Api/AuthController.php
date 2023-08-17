<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function geToken(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        // Se o guard do config que encontra-se no config/auth, permanecer we, podes pôr apartir daqui api. como podes faze-lo apartir do guard, onde esta web e substituir pelo api  'defaults' => ['guard' => 'api','passwords' => 'users',]
        // if ($token = !auth('api')->attempt($credentials)) {
        if (!$token = auth('api')->attempt($credentials)) {
            abort(401, 'Nao Autorizado');
        }

        return response()->json([
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
            //    'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }

    /**
     * Comando Factory: php artisan tinker | User::factory()->create() - executar na linha de comando
     * 
     * Quando quiser fazer listagem, vai no Headers e configura o Accept - Application/json e na lina por baixo: Authorization - Bearer segue o token perto do Bearer.
     * 
     */
}
