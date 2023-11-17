<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|min:11|max:11|unique:users,phone',
            'password' => 'required|confirmed|min:6|max:50',
            'password_confirmation' => 'required|min:6|max:50',
        ]);
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 400);
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ];
        DB::beginTransaction();
        try {
            $user = User::create($data);
            DB::commit();

            // Generate a JWT token
            auth()->login($user);
            $response = [
                'success' => [
                    'success' => 'User registered successfully',
                    'status' => 1,
                ],
            ];
            $errroCode = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'errors' => [
                    'error' => 'Internal Server Error',
                    'message' => $e->getMessage(),
                    'status' => 0,
                ],
            ];
            $errroCode = 400;
        }
        return response()->json($response, $errroCode);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $credentials = $request->only(['email', 'password']);
        try {
            if (!$token = Auth::attempt($credentials)) {
                $response = [
                    'errors' => [
                        'error' => 'User not found ',
                        'status' => 0,
                        'token' => false,
                    ],
                ];
                $errorCode = 401;
            } else {
                $response = [
                    'success' => [
                        'message' => 'User found ',
                        'status' => 1,
                        'authorization' => [
                            'token' => $token,
                            'token_type' => 'bearer',
                            'expires_in' => Auth::factory()->getTTL() * 360
                        ],
                    ],
                ];
                $errorCode = 200;
            }
        } catch (\Exception $e) {
            $response = [
                'errors' => [
                    'message' => "Internal Server Error",
                    'serverError' => $e->getMessage(),
                    'status' => 0,
                ],
            ];
            $errorCode = 500;
        }
        return response()->json($response, $errorCode);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['errors' => ['message' => 'Logged out successfully']], 200);
    }
    public function show(Request $request)
    {
        $query = User::select(['id', 'name', 'email', 'phone', 'created_at']);
        if ($query->count() <= 0) {
            $response = [
                'errors' => [
                    'message' => 'User not exists',
                    'status' => 0,
                ],
            ];
            $errorCode = 401;
        } else {
            $response = [
                'success' => [
                    'message' => $query->count() . ' User exists',
                    'status' => 1,
                    'data' => $query->get(),
                ],
            ];
            $errorCode = 200;
        }
        return response()->json($response, $errorCode);
    }

    public function index($id)
    {
        $findUser = User::select(['id', 'name', 'email', 'phone', 'password', 'created_at'])->where('id', $id)->first();
        if (is_null($findUser)) {
            $response = [
                'errors' => [
                    'message' => 'User not exists',
                    'status' => 0,
                ],
            ];
            $errorCode = 401;
        } else {
            $response = [
                'success' => [
                    'data' => $findUser,
                    'status' => 1,
                ],
            ];
            $errorCode = 200;
        }
        return response()->json($response, $errorCode);
    }

    public function booking()
    {
        try {
            // Attempt to parse and authenticate the user from the JWT token
            $user = JWTAuth::parseToken()->authenticate();

            // Check if the user exists
            if (!$user) {
                return response()->json([
                    'errors' => [
                        'message' => 'User not found',
                        'status' => 0
                    ],
                ], 404);
            }

            // Fetch bookings for the authenticated user with room details
            $bookings = Booking::where('user_id', $user->id)
                ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                ->select('bookings.*', 'rooms.price')
                ->orderBy('bookings.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => [
                    'data' => $bookings,
                    'status' => 1,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'error' => 'Token decoding error',
                    'error_msg' => $e->getMessage()
                ],
            ], 401);
        }
    }

    public function userDetails()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'errors' => [
                        'message' => 'User not found',
                        'status' => 0
                    ],
                ], 404);
            }

            return response()->json([
                'success' => [
                    'data' => $user,
                    'status' => 1,
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'error' => 'Token decoding error',
                    'error_msg' => $e->getMessage()
                ],
            ], 401);
        }
    }


    public function destroy($id)
    {
        $user_id = User::find($id);
        $user_found = json_decode($user_id);
        if (is_null($user_id)) {
            $response =
                [
                    'errors' => [
                        'error' => "User not exists",
                        'status' => 0,
                    ],
                ];
            $errorCode = 400;
        } else {
            if ($user_found->id == 1 || $user_found->id == 2) {
                $response =
                    [
                        'errors' => [
                            'error' => "Only Manager is allowed to Remove Manager and Admin",
                            'status' => 0,
                        ],
                    ];
                $errorCode = 400;
            } else {
                DB::beginTransaction();
                try {
                    $user_id->delete();
                    DB::commit();
                    $response = [
                        'success' => [
                            'message' => 'User Deleted Successfully',
                            'status' => 1,
                        ],
                    ];
                    $errorCode = 200;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $response = [
                        'errors' => [
                            'error' => 'Internal Server Error',
                            'status' => 0,
                            'error_msg' => $e->getMessage()
                        ],
                    ];
                    $errorCode = 500;
                }
            }
        }
        return response()->json($response, $errorCode);
    }
}
