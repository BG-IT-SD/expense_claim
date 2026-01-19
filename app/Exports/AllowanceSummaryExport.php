<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Expense;
use App\Models\Vbookingall;
use Illuminate\Http\Request;

class AllowanceSummaryExport implements FromView, WithStyles, WithColumnFormatting, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**  Query หลัก  */
    private function getAllowanceQuery(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        $search_name = $request->input('search_name');
        $search_plant = $request->input('search_plant');
        $search_department = $request->input('search_department');
        $search_empid = $request->input('search_empid');
        $search_bu = $request->input('search_bu');

        // ค้นหา Plant (จากตาราง booking)
        $bookIds = null;
        if ($search_plant) {
            $bookingQuery = Vbookingall::on('booking_carv2');
            $bookingQuery->where(function ($q) use ($search_plant) {
                $q->where('location_name', 'LIKE', "%{$search_plant}%")
                    ->orWhere('locationbu', 'LIKE', "%{$search_plant}%");
            });
            $bookIds = $bookingQuery->pluck('id');
        }

        // ดึงข้อมูลหลักจาก Expense
        $query = Expense::with([
            'exgroup',
            'user',
            'groupSpecial',
            'finalApprove',
            'vbooking'
        ])
            ->whereHas('approves', function ($q) {
                $q->where('typeapprove', 6)
                    ->where('statusapprove', 1)
                    ->where('deleted', 0)
                    ->whereIn('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('approve')
                            ->groupBy('exid');
                    });
            });

        // ช่วงวันที่จ่าย
        $query->whereHas('exgroup', function ($q) use ($start, $end) {
            if ($start && $end) {
                $q->whereBetween('paymentdate', [$start, $end]);
            }
        });

        // รหัสพนักงาน
        if ($search_empid) {
            $query->where('empid', 'LIKE', "%{$search_empid}%");
        }

        // ค้นหาชื่อ (จาก users หรือ group_specials)
        if ($search_name) {
            $query->where(function ($q) use ($search_name) {
                $q->whereHas('user', function ($sub) use ($search_name) {
                    $sub->where('fullname', 'like', "%{$search_name}%");
                })
                    ->orWhereHas('groupSpecial', function ($sub) use ($search_name) {
                        $sub->where('fullname', 'like', "%{$search_name}%");
                    });
            });
        }

        // ค้นหาหน่วยงาน
        if ($search_department) {
            $query->where(function ($q) use ($search_department) {
                $q->whereHas('user', function ($sub) use ($search_department) {
                    $sub->where('dept', 'like', "%{$search_department}%");
                })
                    ->orWhereHas('groupSpecial', function ($sub) use ($search_department) {
                        $sub->where('dept', 'like', "%{$search_department}%");
                    });
            });
        }

        //ค้นหาจาก exgroup plant_id
        if ($search_bu) {
            $query->whereHas('exgroup', function ($q) use ($search_bu) {
                $q->where('plantid', $search_bu);
            });
        }

        // ค้นหาสถานที่
        if ($bookIds !== null) {
            $query->whereIn('bookid', $bookIds);
        }

        return $query;
    }

    /** View สำหรับ Excel */
    public function view(): View
    {
        $expenses = $this->getAllowanceQuery($this->request)
            ->orderBy('exgroup', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        // dd($expenses);
        $lastGroupId = null;

        return view('exports.allowance_summary_excel', [
            'expenses' => $expenses,
            'start' => $this->request->input('start_date'),
            'end' => $this->request->input('end_date'),
            'lastGroupId' => $lastGroupId,
        ]);
    }

    /**  Style ของ Header */
    public function styles(Worksheet $sheet)
    {
        return [
            'A2:O2' => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    /**  Format ตัวเลข 2 ทศนิยม */
    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER_00,
            'L' => NumberFormat::FORMAT_NUMBER_00,
            'M' => NumberFormat::FORMAT_NUMBER_00,
            'N' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // ให้แถวหัวตาราง (row 2) ติดทุกหน้าตอน Print
                $event->sheet->getDelegate()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(2, 2);
            },
        ];
    }
}
