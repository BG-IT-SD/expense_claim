<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class GroupSpecial extends Model
{
    use HasFactory;
    protected $table = 'group_specials';
    protected $fillable = ['typeid', 'empid', 'fullname', 'position', 'created_by', 'modified_by', 'status', 'deleted'];

    public function Typegroup()
    {
        return $this->belongsTo(Typegroup::class, 'typeid', 'id');
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

        static::updating(function ($userrole) {
            $userrole->modified_by = Auth::id();
        });
    }
}
