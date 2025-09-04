<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAlert extends Model
{
   protected $table = 'messagealert';
   protected $fillable = ['message', 'created_by', 'modified_by', 'status', 'deleted'];
   public $timestamps = true;

     public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy(){
            return $this->belongsTo(User::class, 'modified_by', 'id');
    }
}
