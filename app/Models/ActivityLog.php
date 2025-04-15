<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'action', 'model', 'description',
        'user_id', 'user_name', 'ip_address', 'url'
    ];

}
