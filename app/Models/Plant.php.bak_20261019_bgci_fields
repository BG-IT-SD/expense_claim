<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plant extends Model
{
    use HasFactory;

    protected $table = 'plants';

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'plantname',
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
