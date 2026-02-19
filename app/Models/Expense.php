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
        'latitude_b',
        'longitude_b',
        'map_a_name',
        'map_b_name',
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
        'exgroup',
        'distancemore',
        'distancenote',
        'afdistance',
        'basedistance',
        'departuretime',
        'remarknew'
    ];

    public function latestApprove()
    {
        return $this->hasOne(Approve::class, 'exid', 'id')
            ->where('deleted', 0)
            ->where('status', 1)
            ->latestOfMany(); // ✅ ดึง row ล่าสุด
    }

    public function fuelprice()
    {
        return $this->belongsTo(Fuelprice::class, 'fuelpricesid', 'id');
    }
    public function fuel()
    {
        return $this->belongsTo(FuelPrice91::class, 'fuel91id', 'id');
    }
    public function bookings()
    {
        return $this->belongsTo(Vbookmanage::class, 'bookid', 'id');
    }
    public function logs()
    {
        return $this->hasMany(ExpenseLog::class, 'exid', 'id');
    }
    public function foods()
    {
        return $this->hasMany(ExpenseFood::class, 'exid', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'empid', 'empid');
    }

    public function userhr()
    {
        return $this->belongsTo(Valldataemp::class, 'empid', 'CODEMPID')
            ->where('STAEMP', '!=', 9);
    }

    public function tech()
    {
        return $this->belongsTo(GroupSpecial::class, 'empid', 'empid');
    }
    public function vbooking()
    {
        return $this->hasOne(Vbookingall::class, 'id', 'bookid');
    }

    public function vbookingdrv()
    {
        return $this->hasOne(Vbookingalldrv::class, 'id', 'bookid');
    }

    // public function vbookingreport()
    // {
    //     return $this->hasOne(BookingReport::class, 'id', 'bookid');
    // }
    public function vbookingreport()
    {
        // ตาราง view มีคอลัมน์ id = booking_id
        return $this->hasMany(BookingReport::class, 'id', 'bookid');
    }

    public function approval()
    {
        return $this->hasOne(Approve::class, 'exid', 'id');
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

    public function exgroup()
    {
        return $this->belongsTo(Exgroup::class, 'exgroup', 'id');
    }

    public function approves()
    {
        return $this->hasMany(Approve::class, 'exid', 'id')->where('deleted', 0);
    }

    public function groupSpecial()
    {
        return $this->belongsTo(GroupSpecial::class, 'empid', 'empid');
    }

    public function exgroupData()
    {
        return $this->belongsTo(Exgroup::class, 'exgroup', 'id');
    }


    public function finalApprove()
    {
        return $this->hasOne(Approve::class, 'exid', 'id')
            ->where('deleted', 0)
            ->where('statusapprove', 1)
            ->where('typeapprove', 6)
            ->latestOfMany(); //ใช้ตัวล่าสุดเท่านั้น
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

    public function getGoogleMapsUrlAttribute(): ?string
    {
        // เช็คว่ามีค่าครบทั้ง 4 จุดหรือไม่
        if (!$this->latitude || !$this->longitude || !$this->latitude_b || !$this->longitude_b) {
            return null;
        }

        $params = [
            'api' => 1,
            'origin' => "{$this->latitude},{$this->longitude}",
            'destination' => "{$this->latitude_b},{$this->longitude_b}",
            'travelmode' => 'driving'
        ];

        return "https://www.google.com/maps/dir/?" . http_build_query($params);
    }
}
