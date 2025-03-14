<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fuelprice extends Model
{
    use HasFactory;

    protected $table = 'fuelprices';

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'startrate',
        'endrate',
        'bathperkm',
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

}
