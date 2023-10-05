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
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
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
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }
        DB::beginTransaction();
        try {
            Room::Create($request->all());
            DB::commit();
            $response = [
                'success' => [
                    'message' => 'Room stored successfully',
                    'status' => 1,
                ]
            ];
            $errorCode = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'errors' => [
                    'message' => 'Internal server error',
                    'error' => $e->getMessage(),
                    'status' => 0,
                ]
            ];
            $errorCode = 500;
        }
        return response()->json($response, $errorCode);
    }

    public function show()
    {
        return response()->json([
            'success' => [
                'details' => Room::all(),
            ],
            'status' => 1,
        ], 200);
    }

    public function index($id)
    {
        $findRoom = Room::find($id);
        if (is_null($findRoom)) {
            $response = [
                'errors' => [
                    'message' => 'Room not exists!',
                    'status' => 0,
                ],
            ];
            $errorCode = 401;
        } else {
            $response = [
                'success' => [
                    'data' => $findRoom,
                    'status' => 1,
                ],
            ];
            $errorCode = 200;
        }
        return response()->json($response, $errorCode);
    }

    public function destroy(Request $request, $id)
    {

        $room = Room::find($id);
        DB::beginTransaction();
        try {
            $room->delete();
            DB::commit();
            $response = [
                'success' => [
                    'message' => 'Room Deleted Successfully',
                    'status' => 1,
                ]
            ];
            $errorCode = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'errors' => [
                    'message' => 'Internal server error',
                    'error' => $e->getMessage(),
                    'status' => 0,
                ]
            ];
            $errorCode = 500;
        }

        return response()->json($response, $errorCode);
    }

    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (is_null($room)) {
            $response = [
                'errors' => [
                    'error' => "Room not exists!",
                    'status' => 0,
                ]
            ];
            $errorCode = 400;
        }
        DB::beginTransaction();
        try {
            $room->update($request->all());
            DB::commit();
            $response = [
                'success' => [
                    'message' => 'Room Updated Successfully',
                    'status' => 1,
                ]
            ];
            $errorCode = 200;
        } catch (\Exception $e) {
            DB::rollBack();

            $response = [
                'errors' => [
                    'error' => 'Internal server error',
                    'message' => $e->getMessage(),
                    'status' => 0,
                ]
            ];
            $errorCode = 500;
        }

        return response()->json($response, $errorCode);
    }
}
