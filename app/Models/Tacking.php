<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tacking extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'tracking';
}
