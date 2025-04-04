<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    protected $connection = 'mysql';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'expenses';

    protected $fillable = [
        'id',
        'prefix',
        'bookid',
        'empid',
        'extype',
        'departurefrom',
        'departureplant',
        'departuretext',
        'returnfrom',
        'returnplant',
        'returnfromtext',
        'returntime',
        'totaldistance',
        'latitude',
        'longitude',
        'checktoil',
        'fuel91id',
        'fuelpricesid',
        'publictransportfare',
        'expresswaytoll',
        'otherexpenses',
        'costoffood',
        'travelexpenses',
        'gasolinecost',
        'totalprice',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

    public function latestApprove()
    {
        return $this->hasOne(Approve::class, 'exid', 'id')
        ->where('deleted', 0)
        ->where('status', 1)
        ->latestOfMany(); // ✅ ดึง row ล่าสุด
    }

    public function Departureplant()
    {
        return $this->belongsTo(Plant::class, 'departureplant', 'id');
    }

    public function Returnplant()
    {
        return $this->belongsTo(Plant::class, 'returnplant', 'id');
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
