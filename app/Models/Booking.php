<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'room_id',
        'reservation_id',
        'start_date',
        'end_date',
        'total_amount',
        'payment_status',
        'payment_method',
        'status',
        'number_of_guests'
    ];
}
