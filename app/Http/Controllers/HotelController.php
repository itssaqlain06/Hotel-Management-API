<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function store(Request $request){
        $valdidate=Validator::make($request->all(),[
            'name' => 'required',
            'description' => 'required',
            'star_rating'=> 'required',
            'country' =>'required',
            'state' =>'required',
            'city' =>'required',
            'address' =>'required',
            'phone' =>'required',
            'email' => 'required',
            'checkin_time' => 'required',
            'checkout_time' =>'required',
            'user_id' => 'required'
        ]);
        if($valdidate->fails()){
            return response()->json(
                [
                'message' =>$valdidate->errors(),
                'status' => 0
                ],
                400
            );
        }
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
            'user_id' => User::find($request->user_id)
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
               'message' => $e->getMessage(),
              'status' => 0
            ];
            $errorCode=500;
        }
        return response()->json($response,$errorCode);
    }
}
