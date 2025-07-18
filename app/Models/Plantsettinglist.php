<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantsettinglist extends Model
{
    protected $table = 'plantsettinglist';
    protected $fillable = [
        'headid',
        'empid',
        'etc',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by', 'id');
    }
}
