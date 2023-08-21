<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'star_rating',
        'country',
        'state',
        'city',
        'address',
        'phone',
        'email',
        'checkin_time',
        'checkout_time',
        'user_id'
    ];

    public function user(){
        return $this->hasMany(User::class);
    }
}
