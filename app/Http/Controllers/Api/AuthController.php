<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNull;

class AuthController extends Controller
{
    public function register(Request $request){
        $validate=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'phone'=>'required|min:11|max:11',
            'password'=>'required|confirmed|min:6|max:50',
            'password_confirmation'=>'required|min:6|max:50'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
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
                'message' => 'Internal server error',
                'error'=>$e->getMessage(),
                'status' =>0
            ];
            $errroCode = 400;
        }
        return response()->json($response,$errroCode);
    }

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
                'authorization' =>[
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => Auth::factory()->getTTL() * 360
                ]
            ];
            $errorCode=200;
        }

        return response()->json($response,$errorCode);
    }

    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'Logged out successfully'],200);
    }
    public function show(Request $request){
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

    public function index($id){
        $findUser=User::select(['name','email'])->where('id',$id)->first();
        if(is_null($findUser)){
            $response=[
               'message' => 'User not exists!',
               'status' => 0,
             ];
             $errorCode=401;
        }else{
                $response =[
                    'data' => $findUser,
                  'status' => 1
                ];
                $errorCode=200;
            }
        return response()->json($response,$errorCode);
    }

    public function destroy($id){
        $user_id = User::find($id);
        $user_found=json_decode($user_id);
        if(is_null($user_id)){
            $response=
                [
                'error' => "User not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            if($user_found->id==1 || $user_found->id==2){
                $response=
                    [
                    'error' => "Only Manager is allowed to Remove Manager and Admin",
                    'status' => 0
                    ];
                    $errorCode=400;
            }else{
                DB::beginTransaction();
                try{
                    $user_id->delete();
                    DB::commit();
                    $response=[
                    'message' => 'User Deleted Successfully',
                    'status' => 1
                    ];
                    $errorCode=200;
                }catch(\Exception $e){
                    DB::rollBack();
                    $response=[
                    'error' => 'Internal server error',
                    'status' => 0
                    ];
                    $errorCode=500;
                }
            }
        }
        return response()->json($response,$errorCode);
    }
}
