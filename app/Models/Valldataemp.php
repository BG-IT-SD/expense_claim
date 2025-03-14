<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valldataemp extends Model
{

    protected $connection = 'mysql_hrms';
    // protected $view = 'v_alldataemp';
    protected $table = 'v_alldataemp';
    protected $fillable = [
        'CODEMPID',
        'NAMFIRSTT',
        'NAMLASTENG',
        'EMAIL',
        'alias_name',
        'STAEMP',
        'DEPT',
        'NUMOFFID'
    ];
    protected $primaryKey = 'CODEMPID';

    public $incrementing = false;
    public $timestamps = false;
}
