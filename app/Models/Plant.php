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
        'plant_code',
        'plantname',
        'province',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

    public function getDisplayNameAttribute(): string
    {
        if (in_array($this->plant_code, ['BGCI-PT', 'BGCI-AY'], true) && !empty($this->province)) {
            return "BGCI ({$this->province})";
        }

        return $this->plantname;
    }

public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
}

public function ModifiedBy(){
        return $this->belongsTo(User::class, 'modified_by', 'id');
}
}
