<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_id',
        'booking_id',
        'start_date',
        'end_date',
        'number_of_guests',
        'status',
        'special_request'
    ];
}
