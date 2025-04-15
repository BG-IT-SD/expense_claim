<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'passenger';

    public function expense()
{
    return $this->hasOne(Expense::class, 'bookid', 'booking_id');
}

}
