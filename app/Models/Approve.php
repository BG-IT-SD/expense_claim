<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Approve extends Model
{
    protected $table = 'approve';

    protected $fillable = [
        'exid',
        'typeapprove',
        'empid',
        'email',
        'approvename',
        'emailstatus',
        'statusapprove',
        'created_by',
        'modified_by',
        'status',
        'deleted',
        'login_token',
        'token_expires_at',
        'remark',
        'exgroup',
    ];


    public function expense()
    {
        return $this->belongsTo(Expense::class, 'exid', 'id');
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
