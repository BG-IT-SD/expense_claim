<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantsettingdetail extends Model
{
    protected $table = 'plantsettingdetail';
    protected $fillable = [
        'headid',
        'plantid',
        'etc',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

public function plant(){
    return $this->belongsTo(Plant::class, 'plantid', 'id');
}

public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
}

public function ModifiedBy(){
        return $this->belongsTo(User::class, 'modified_by', 'id');
}
}
