<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class DistanceRate extends Model
{
    use HasFactory;

    protected $table = 'distance_rates';

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'startplant',
        'endplant',
        'kilometer',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

    public function Startplant()
    {
        return $this->belongsTo(Plant::class, 'startplant', 'id');
    }

    public function Endplant()
    {
        return $this->belongsTo(Plant::class, 'endplant', 'id');
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
