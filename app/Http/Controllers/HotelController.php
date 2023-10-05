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
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|unique:hotels,name',
            'description' => 'required',
            'star_rating' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'checkin_time' => 'required',
            'checkout_time' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()],400);
        } else {
            $user_id = '1';
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'star_rating' => $request->star_rating,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'checkin_time' => $request->checkin_time,
                'checkout_time' => $request->checkout_time,
                'user_id' => $user_id
            ];
            DB::beginTransaction();
            try {
                $hotel = Hotel::create($data);
                DB::commit();
                $response = [
                    'success' => [
                        'message' => 'Hotel Created Successfully',
                        'status' => 1,
                        'data' => $hotel
                    ]
                ];
                $errorCode = 200;
            } catch (\Exception $e) {
                DB::rollBack();
                $response = [
                    'errors' => [
                        'message' => 'Internal server error',
                        'serverError' => $e->getMessage(),
                        'status' => 0
                    ]
                ];
                $errorCode = 500;
            }
        }
        return response()->json($response, $errorCode);
    }

    public function show(Request $request)
    {
        return response()->json(
            [
                'success' => [
                    'details' => Hotel::all()
                ],
                'status' => 1
            ],
            200
        );
    }

    public function index($id)
    {
        $findHotel = Hotel::find($id);
        if (is_null($findHotel)) {
            $response = [
                'errors' => [
                    'message' => 'Hotel not exists!',
                    'status' => 0,
                ]
            ];
            $errorCode = 401;
        } else {
            $response = [
                'success' => [
                    'data' => $findHotel,
                    'status' => 1
                ]
            ];
            $errorCode = 200;
        }
        return response()->json($response, $errorCode);
    }

    public function destroy(Request $request, $id)
    {

        $hotel = Hotel::find($id);
        if (is_null($hotel)) {
            $response =
                [
                    'errors' => [
                        'error' => "Hotel not exists!",
                        'status' => 0
                    ]
                ];
            $errorCode = 400;
        } else {
            DB::beginTransaction();
            try {
                $hotel->delete();
                DB::commit();
                $response = [
                    'success' => [
                        'message' => 'Hotel Deleted Successfully',
                        'status' => 1
                    ]
                ];
                $errorCode = 200;
            } catch (\Exception $e) {
                DB::rollBack();
                $response = [
                    'errors' => [
                        'message' => 'Internal server error',
                        'error' => $e->getMessage(),
                        'status' => 0
                    ]
                ];
                $errorCode = 500;
            }
        }
        return response()->json($response, $errorCode);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);

        if (is_null($hotel)) {
            $response = [
                'errors' => [
                    'error' => "Hotel not exists!",
                    'status' => 0
                ]
            ];
            $errorCode = 400;
        } else {
            DB::beginTransaction();
            try {
                $hotel->update($request->all());
                DB::commit();
                $response = [
                    'success' => [
                        'message' => 'Hotel Updated Successfully',
                        'status' => 1
                    ]
                ];
                $errorCode = 200;
            } catch (\Exception $e) {
                DB::rollBack();

                $response = [
                    'errors' => [
                        'message' => 'Internal server error',
                        'error' => $e->getMessage(),
                        'status' => 0
                    ]
                ];
                $errorCode = 500;
            }
        }
        return response()->json($response, $errorCode);
    }
}
