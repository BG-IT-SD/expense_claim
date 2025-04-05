<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Userrole extends Model
{
    use HasFactory;
    protected $table = 'user_roles';

    protected $fillable = ['userid', 'moduleid', 'roleid', 'status', 'deleted', 'created_by', 'modified_by'];

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by', 'id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'moduleid', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleid', 'id');
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
