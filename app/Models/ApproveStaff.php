<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class ApproveStaff extends Model
{
    use HasFactory;
    protected $table ='approvestaff';

    protected $fillable = ['extype','step','group','empid','email','fullname','status', 'deleted' , 'created_by', 'modified_by'];

    public function plantSettingDetails()
    {
        return $this->hasMany(PlantSettingDetail::class, 'headid', 'group');
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

    public function getStepTextAttribute(){
        return match ($this->step){
            1 => 'ผู้จัดการส่วน',
            2 => 'ผู้จัดการฝ่าย',
            3 => 'ผู้ตรวจสอบ',
            default => 'Unknow',
        };
    }
}
