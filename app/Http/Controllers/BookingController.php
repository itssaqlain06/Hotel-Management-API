<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required',
            'hotel_id' => 'required',
            'room_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_guests' => 'required',
        ]);

        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 400);
        }

        // Check if user exists
        $findUser = User::find($request->user_id);
        if (is_null($findUser)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if hotel exists
        $findHotel = Hotel::find($request->hotel_id);
        if (is_null($findHotel)) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        // Check if room exists
        $findRoom = Room::find($request->room_id);
        if (is_null($findRoom)) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        // Validate JWT authentication
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token decoding error'], 401);
        }

        // Check user authorization
        if ($user->id !== 1 && $user->id !== 2) {
            return response()->json(['message' => 'You are not authorized to perform this action'], 403);
        }

        // Check room availability
        $roomAvailable = $this->checkRoomAvailability($request->room_id, $request->start_date, $request->end_date);
        if (!$roomAvailable) {
            return response()->json(['message' => 'Room is not available for the selected dates'], 400);
        }
        $start_date = new DateTime($request->start_date);
        $end_date = new DateTime($request->end_date);
        $interval = $start_date->diff($end_date);
        $days_difference = $interval->days;
        $total_amount = $findRoom->price * $days_difference;

        // Create booking
        try {
            DB::beginTransaction();
            $data = [
                'room_id' => $request->room_id,
                'user_id' => $request->user_id,
                'hotel_id' => $request->hotel_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'number_of_guests' => $request->number_of_guests,
                'total_amount' => $total_amount,
                'status' => 1,
            ];
            $booking_done = Booking::create($data);
            DB::commit();
            return response()->json(['message' => 'Booking created successfully', 'data' => $booking_done], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function show()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token decoding error'], 401);
        }
        if ($user->id !== 1 && $user->id !== 2) {
            return response()->json(['message' => 'You are not authorized to perform this action'], 403);
        }
        return response()->json([
            'All booking details' => [
                'booking' => Booking::all()
            ],
            'status' => 1
        ], 200);
    }

    public function index($id){
        $findBooking=Booking::find($id);
        if(is_null($findBooking)){
            $response=[
               'message' => 'Booking not exists!',
               'status' => 0,
             ];
             $errorCode=401;
        }else{
                $response =[
                    'booking' => $findBooking,
                    'status' => 1
                ];
                $errorCode=200;
            }
        return response()->json($response,$errorCode);
    }

    protected function checkRoomAvailability($roomId, $startDate, $endDate, $excludeBookingId = null)
    {
        $bookingsQuery = Booking::where('room_id', $roomId)
            ->where(function ($query) use ($startDate, $endDate, $excludeBookingId) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
                if ($excludeBookingId !== null) {
                    $query->where('id', '<>', $excludeBookingId);
                }
            });

        $bookingsCount = $bookingsQuery->count();

        return $bookingsCount === 0;
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 0
            ], 400);
        }

        $booking = Booking::find($id);
        if (is_null($booking)) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        $findRoom = $booking->room_id;
        $findRoom=Room::find($findRoom);

        // Check room availability for the updated dates
        $roomAvailable = $this->checkRoomAvailability($booking->room_id, $request->start_date, $request->end_date,$id);
        if (!$roomAvailable) {
            return response()->json(['message' => 'Room is not available for the updated dates'], 400);
        }

        // Validate user authentication
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token decoding error'], 401);
        }

        // Check user authorization
        if ($user->id !== 1 && $user->id !== 2) {
            return response()->json(['message' => 'You are not authorized to perform this action'], 403);
        }

        DB::beginTransaction();
        try {
            $start_date = new DateTime($request->start_date);
            $end_date = new DateTime($request->end_date);
            $interval = $start_date->diff($end_date);
            $days_difference = $interval->days;
            $total_amount = $findRoom->price * $days_difference;

            $data=[
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'number_of_guests' => $request->number_of_guests,
                'total_amount' => $total_amount,
                'special_requests' => $request->special_requests
            ];
            $booking->update($data);
            DB::commit();
            $response = [
                'message' => 'Booking Updated Successfully',
                'data' => $booking,
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
        return response()->json($response, $errorCode);
    }

    public function cancel($id)
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token decoding error'], 401);
        }

        if ($user->id !== 1 && $user->id !== 2) {
            return response()->json(['message' => 'You are not authorized to perform this action'], 403);
        }

        if ($booking->status == 1) {
            $booking->status = 0;
            $booking->save();

            return response()->json(['message' => 'Booking canceled successfully'], 200);
        } else {
            return response()->json(['message' => 'Booking cannot be canceled'], 400);
        }
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token decoding error'], 401);
        }

        if ($user->id !== 1 && $user->id !== 2) {
            return response()->json(['message' => 'You are not authorized to perform this action'], 403);
        }
        DB::beginTransaction();
        try {
            $booking->delete();
            DB::commit();
            $response = [
                'message' => 'Booking Deleted Successfully',
                'status' => 1
            ];
            $errorCode = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
                'status' => 0
            ];
            $errorCode = 500;
        }

        return response()->json($response, $errorCode);
    }

}
