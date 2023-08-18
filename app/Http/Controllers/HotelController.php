<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function store(Request $request){
        $valdidate=Validator::make($request->all(),[
            'name' => 'required|unique:hotels,name',
            'description' => 'required',
            'star_rating'=> 'required',
            'country' =>'required',
            'state' =>'required',
            'city' =>'required',
            'address' =>'required',
            'phone' =>'required|unique:hotels,phone',
            'email' => 'required|unique:hotels,email',
            'checkin_time' => 'required',
            'checkout_time' =>'required',
            'user_id' => 'required'
        ]);
        if($valdidate->fails()){
            return response()->json(
                [
                'error' =>$valdidate->errors(),
                'status' => 0
                ],
                400
            );
        }
        $user_id = User::find($request->user_id);
        $user_found=json_decode($user_id);
        if(is_null($user_id)){
            $response=
                [
                'error' => "User not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            if($user_found->id!=1){
                $response=
                    [
                    'error' => "Only Manager is allowed to Create Hotel",
                    'status' => 0
                    ];
                    $errorCode=400;
            }else{
                $data =[
                    'name' => $request->name,
                    'description' => $request->description,
                    'star_rating' => $request->star_rating,
                    'country' =>$request->country,
                    'state' =>$request->state,
                    'city' =>$request->city,
                    'address' =>$request->address,
                    'phone' =>$request->phone,
                    'email' => $request->email,
                    'checkin_time' => $request->checkin_time,
                    'checkout_time' =>$request->checkout_time,
                    'user_id' =>$user_id->id
                ];
                DB::beginTransaction();
                try{
                    $hotel=Hotel::create($data);
                    DB::commit();
                    $response=[
                        'message' => 'Hotel Created Successfully',
                        'status' => 1,
                        'hotel' => $hotel
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

    public function show(Request $request){
        return response()->json([
            'All hotel details' => [
                'Hotel details' => Hotel::all()
            ],
          'status' => 1
        ],
        200
    );
    }

    public function destroy(Request $request){
        $valdidate=Validator::make($request->all(),[
            'id' =>'required',
            'user_id' =>'required'
        ]);
        if($valdidate->fails()){
            return response()->json(
                [
                'error' =>$valdidate->errors(),
               'status' => 0
                ],
                400
            );
        }
        $user_id = User::find($request->user_id);
        $user_found=json_decode($user_id);
        if(is_null($user_id)){
            $response=
                [
                'error' => "User not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            if($user_found->id!=1){
                $response=
                    [
                    'error' => "Only Manager is allowed to Update Hotel details",
                    'status' => 0
                    ];
                    $errorCode=400;
        }else{
            $hotel=Hotel::find($request->id);
        if(is_null($hotel)){
            $response=
                [
                'error' => "Hotel not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            DB::beginTransaction();
            try{
                $hotel->delete();
                DB::commit();
                $response=[
                'message' => 'Hotel Deleted Successfully',
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
        }
        return response()->json($response,$errorCode);
    }

    public function update(Request $request){
        $validate = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
                'status' => 0
            ], 400);
        }

        $user_id = User::find($request->user_id);
        $user_found=json_decode($user_id);
        if(is_null($user_id)){
            $response=
                [
                'error' => "User not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            if($user_found->id!=1){
                $response=
                    [
                    'error' => "Only Manager is allowed to Update Hotel details",
                    'status' => 0
                    ];
                    $errorCode=400;
            }else{
                $hotel = Hotel::find($request->id);

                if (is_null($hotel)) {
                    $response = [
                        'error' => "Hotel not exists!",
                        'status' => 0
                    ];
                    $errorCode = 400;
                } else {
                    DB::beginTransaction();
                    try {
                        $hotel->update($request->all());
                        DB::commit();
                        $response = [
                            'message' => 'Hotel Updated Successfully',
                            'status' => 1
                        ];
                        $errorCode = 200;
                    } catch (\Exception $e) {
                        DB::rollBack();

                        $response = [
                            'error' => 'Internal server error',
                            'message' => $e->getMessage(),
                            'status' => 0
                        ];
                        $errorCode = 500;
                    }
                }
            }
        }
        return response()->json($response, $errorCode);
    }
}
