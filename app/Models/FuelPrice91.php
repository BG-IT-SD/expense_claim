<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FuelPrice91 extends Model
{
    use HasFactory;

    protected $table = 'fuel_price91s';

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'dateprice',
        'price',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
}

public function ModifiedBy(){
        return $this->belongsTo(User::class, 'modified_by', 'id');
}

    public $timestamps = true;
}
