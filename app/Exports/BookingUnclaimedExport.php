<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BookingUnclaimedExport implements FromView
{
    public function __construct(
        private $rows,
        private array $filters = []
    ) {}

    public function view(): View
    {
        return view('exports.hr_unclaimed_booking_excel', [
            'rows' => $this->rows,
            'filters' => $this->filters,
        ]);
    }
}
