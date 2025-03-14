<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groupplant extends Model
{
    use HasFactory;

    protected $table = 'groupplants';

    protected $fillable = [
        'mealid',
        'plantid',
        'created_by',
        'modified_by',
        'status',
        'deleted',
    ];

    public $timestamps = true;

    public function meal()
    {
        return $this->belongsTo(Pricepermeal::class, 'mealid', 'id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plantid', 'id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function ModifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by', 'id');
    }
}
