<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_no',
        'description',
        'price',
        'capacity',
        'type',
        'amenities',
        'hotel_id',
        'is_available',
        'is_smoking_allowed',
        'has_balcony',
        'has_pool_access',
        'has_room_service',
    ];
}
