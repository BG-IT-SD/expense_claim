<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HrExport implements FromView
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function view(): View
    {
        return view('exports.hr-list', [
            'expenses' => $this->expenses
        ]);
    }
}