<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    //
    protected $fillable = ['levelname', 'created_by', 'modified_by', 'status', 'deleted'];
    public $timestamps = true;

    public function groupprices()
    {
        return $this->hasMany(Groupprice::class, 'levelid');
    }
}
