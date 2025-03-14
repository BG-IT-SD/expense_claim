<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricepermeal extends Model
{

    use HasFactory;
    protected $table ='pricepermeals';

    protected $fillable = ['groupid','meal1','meal2','meal3','meal4','status', 'deleted' , 'created_by', 'modified_by'];

    public function group()
    {
        return $this->belongsTo(Groupprice::class, 'groupid', 'id');
    }

    public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy(){
            return $this->belongsTo(User::class, 'modified_by', 'id');
    }
}
