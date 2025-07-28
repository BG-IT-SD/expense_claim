<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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

protected static function boot()
{
    parent::boot();
    static::creating(function ($userrole) {
        $userrole->created_by = Auth::id();
    });

    // Set updated_by when updating
    static::updating(function ($userrole) {
        $userrole->modified_by = Auth::id();
    });
}
}
