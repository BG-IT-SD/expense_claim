<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExpenseFood extends Model
{
    protected $table = 'expensefoods';

    protected $fillable = [
        'exid',
        'mealid',
        'meal1',
        'meal2',
        'meal3',
        'meal4',
        'meal1reject',
        'meal2reject',
        'meal3reject',
        'meal4reject',
        'totalpricebf',
        'totalreject',
        'totalprice',
        'used_date',
        'bookid',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];


    public function Mealid()
    {
        return $this->belongsTo(Pricepermeal::class, 'mealid', 'id');
    }

    public function Exid()
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
