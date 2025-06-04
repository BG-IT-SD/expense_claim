<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vbookingall extends Model
{
    protected $connection = 'booking_carv2';
    protected $table = 'v_booking_expense_n7';

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
}
