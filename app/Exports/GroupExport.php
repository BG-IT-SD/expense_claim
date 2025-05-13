<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Exgroup;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class GroupExport implements FromView
{
    protected $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function view(): View
    {
        $exgroup = Exgroup::findOrFail($this->id);
        $expenses = Expense::with(['vbooking', 'user'])->where('exgroup', $this->id)->get();

        return view('exports.group_excel', [
            'exgroup' => $exgroup,
            'expenses' => $expenses,
        ]);
    }
}
