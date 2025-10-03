<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vbookingalldrv extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'v_booking_expense_n7_drv';

        public function expense()
    {
        return $this->hasOne(Expense::class, 'bookid', 'id');
    }

    // public function expense()
    // {
    //     return $this->hasOne(Expense::class, 'bookid', 'id')
    //         ->whereHas('latestApprove', function ($q) {
    //             $q->where('statusapprove', '!=', 2); // ไม่ใช่ reject
    //         })
    //         ->latestOfMany();
    // }

    public function getDisplayLocationAttribute()
    {
        return $this->locationid == 12
            ? $this->location_name
            : $this->locationbu;
    }
}
