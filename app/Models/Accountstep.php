<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Accountstep extends Model
{
    protected $table = 'accountstep';

    protected $fillable = [
        'headid',
        'step',
        'empid',
        'email',
        'fullname',
        'etc',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

    public function plantSettingDetails()
    {
        return $this->hasMany(Accountplant::class, 'headid', 'headid');
    }


    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by', 'id');
    }

    // ตั้งค่า 'create_by' อัตโนมัติเมื่อสร้างข้อมูลใหม่
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
