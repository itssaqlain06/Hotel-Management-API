<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validate=Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required',
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }

        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::attempt($credentials)) {
            $response=[
                'message' => 'User not found !',
                'status' => 0,
                'token' =>false
            ];
            $errorCode=401;
        }else{
            $response =[
                'message' => 'User found !',
                'status' => 1,
                'authorisation' =>[
                    'token' => $token,
                    'token_type' => 'bearer',
                    // 'expires_in' => Auth::factory()->getTTL() * 60
                ]
            ];
            $errorCode=200;
        }

        return response()->json($response,$errorCode);
    }
    public function register(Request $request){
        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|confirmed|min:6|max:50',
            'password_confirmation'=>'required|min:6|max:50'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        DB::beginTransaction();
        try{
            $user=User::create($data);
            DB::commit();

             // Generate a JWT token
            auth()->login($user);
            $response =[
                'message'=> 'User registered successfully !',
                'status' =>1
            ];
            $errroCode=200;
        }catch(\Exception $e){
            DB::rollBack();
            $response =[
                'message'=>$e->getMessage(),
                'status' =>0
            ];
            $errroCode = 400;
        }
        return response()->json($response,$errroCode);
    }
    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Logged out successfully'],200);
    }

    public function user(Request $request){
        $query = User::select(['name','email']);
        if($query->count()<=0){
            $response=[
                'message' => 'User not exists!',
                'status' => 0,
             ];
             $errorCode=401;
        }else{
            $response =[
               'message' => $query->count().' User exists!',
               'status' => 1,
                'data' => $query->get()
            ];
            $errorCode=200;
        }
        return response()->json($response,$errorCode);
    }
}
