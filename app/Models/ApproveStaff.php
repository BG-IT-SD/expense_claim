<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApproveStaff extends Model
{
    use HasFactory;
    protected $table ='approvestaff';

    protected $fillable = ['extype','step','group','empid','email','fullname','status', 'deleted' , 'created_by', 'modified_by'];

    public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy(){
            return $this->belongsTo(User::class, 'modified_by', 'id');
    }
}
