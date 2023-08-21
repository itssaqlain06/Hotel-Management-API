<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoomController extends Controller
{
    public function store(Request $request){
        $validate=Validator::make($request->all(),[
            'room_no' => 'required|unique:rooms,room_no',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'capacity' => 'required|integer',
            'type' => 'required|in:standard,deluxe,suite',
            'hotel_id' => 'required|exists:hotels,id',
            // 'is_smoking_allowed' => 'required|boolean',
            // 'has_balcony' => 'required|boolean',
            // 'has_pool_access' => 'required|boolean',
            // 'has_room_service' => 'required|boolean'
        ]);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }else{
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Token decoding error'], 401);
            }
            if ($user->id !== 1 && $user->id!==2) {
                return response()->json(['message' => 'You are not authorized to perform this action'], 403);
            }else{
                DB::beginTransaction();
                try{
                    Room::Create($request->all());
                    DB::commit();
                    $response=[
                        'message'=> 'Room stored successfully',
                        'status' => 1
                    ];
                    $errorCode=200;
                }catch(\Exception $e){
                    DB::rollBack();
                    $response=[
                    'message' => 'Internal server error',
                    'error' => $e->getMessage(),
                    'status' => 0
                    ];
                    $errorCode=500;
                }
            }
        }
        return response()->json($response, $errorCode);
    }

    public function show()
    {
        return response()->json([
            'All rooms details' => [
                'details' => Room::all()
            ],
            'status' => 1
        ], 200);
    }

    public function index($id){
        $findRoom=Room::find($id);
        if(is_null($findRoom)){
            $response=[
               'message' => 'Room not exists!',
               'status' => 0,
             ];
             $errorCode=401;
        }else{
                $response =[
                    'data' => $findRoom,
                    'status' => 1
                ];
                $errorCode=200;
            }
        return response()->json($response,$errorCode);
    }

    public function destroy(Request $request,$id){
        $valdidate=Validator::make($request->all(),[
            'hotel_id' =>'required'
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
        $hotel_id = Hotel::find($request->hotel_id);
        $hotel_id=json_decode($hotel_id);
        if(is_null($hotel_id)){
            $response=
                [
                'error' => "Hotel not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            $room=Room::find($id);
            if(is_null($room)){
                $response=
                    [
                    'error' => "Room not exists!",
                'status' => 0
                    ];
                $errorCode= 400;
            }else{
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                } catch (\Throwable $e) {
                    return response()->json(['message' => 'Token decoding error'], 401);
                }
                if ($user->id !== 1 && $user->id!==2) {
                    return response()->json(['message' => 'You are not authorized to perform this action'], 403);
                }else{
                    DB::beginTransaction();
                    try{
                        $room->delete();
                        DB::commit();
                        $response=[
                        'message' => 'Room Deleted Successfully',
                        'status' => 1
                        ];
                        $errorCode=200;
                    }catch(\Exception $e){
                        DB::rollBack();
                        $response=[
                        'message' => 'Internal server error',
                        'error' =>$e->getMessage(),
                        'status' => 0
                        ];
                        $errorCode=500;
                    }
                }
            }
        }
        return response()->json($response,$errorCode);
    }

    public function update(Request $request,$id){
        $validate = Validator::make($request->all(), [
            'hotel_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->errors(),
                'status' => 0
            ], 400);
        }

        $hotel_id = Hotel::find($request->hotel_id);
        $hotel_id=json_decode($hotel_id);
        if(is_null($hotel_id)){
            $response=
                [
                'error' => "Hotel not exists!",
              'status' => 0
                ];
               $errorCode= 400;
        }else{
            $room = Room::find($id);
                if (is_null($room)) {
                    $response = [
                        'error' => "Room not exists!",
                        'status' => 0
                    ];
                    $errorCode = 400;
                } else {
                    try {
                        $user = JWTAuth::parseToken()->authenticate();
                    } catch (\Throwable $e) {
                        return response()->json(['message' => 'Token decoding error'], 401);
                    }
                    if ($user->id !== 1 && $user->id!==2) {
                        return response()->json(['message' => 'You are not authorized to perform this action'], 403);
                    }else{
                         DB::beginTransaction();
                    try {
                        $room->update($request->all());
                        DB::commit();
                        $response = [
                            'message' => 'Room Updated Successfully',
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
