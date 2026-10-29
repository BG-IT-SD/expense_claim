<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'created_by',
        'modified_by',
    ];
}
