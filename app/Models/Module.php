<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table ='modules';

    protected $fillable = ['modulename','etc','status', 'deleted' , 'created_by', 'modified_by'];

    public function CreatedBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy(){
            return $this->belongsTo(User::class, 'modified_by', 'id');
    }
}
