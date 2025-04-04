<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vbooking extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'v_booking_expense';

    public function expense()
{
    return $this->hasOne(Expense::class, 'bookid', 'id');
}
}


