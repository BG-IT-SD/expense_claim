<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseLog extends Model
{
        protected $table = 'expense_logs';
        protected $fillable = ['exid','bookid','foodid', 'empid', 'type', 'remark', 'created_at', 'updated_at'];
        public $timestamps = true;

        public function booking()
        {
            return $this->belongsTo(Vbookmanage::class, 'bookid', 'id');
        }

}
