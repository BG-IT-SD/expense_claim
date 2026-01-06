<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'booking';
     public $timestamps = false;

        protected $fillable = [
        'departure_time',
        'return_time',
    ];

}
