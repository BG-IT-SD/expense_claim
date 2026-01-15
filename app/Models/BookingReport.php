<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReport extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'v_booking_expense_report';

    public function expense()
{
    return $this->hasOne(Expense::class, 'bookid', 'id')
    ->latest('id');
}

 public function getDisplayLocationAttribute()
    {
        return $this->locationid == 12
            ? $this->location_name
            : $this->locationbu;
    }
}
