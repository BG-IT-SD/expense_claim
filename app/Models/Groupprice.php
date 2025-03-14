<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupprice extends Model
{

    use HasFactory;
    protected $fillable = ['groupname','levelid', 'created_by', 'modified_by','status', 'deleted'];
    public $timestamps = true;

    public function level()
    {
        return $this->belongsTo(Level::class, 'levelid','id');
    }

    public function pricepermeal()
    {
        return $this->hasOne(Pricepermeal::class, 'groupid', 'id');
    }

    public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy(){
            return $this->belongsTo(User::class, 'modified_by', 'id');
    }

}
