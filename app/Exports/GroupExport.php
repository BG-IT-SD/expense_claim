<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Exgroup;
use App\Models\Expense;
use App\Models\Sigfile;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet; // ใช้ได้ถ้าจะไปตั้ง merge/ความสูงเพิ่มเติม

class GroupExport implements FromView, WithStyles, WithDrawings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $exgroup = Exgroup::findOrFail($this->id);

        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr', 'latestApprove'])
            ->whereHas('latestApprove', function ($query) {
                $query->where('typeapprove', 6)
                    ->whereIn('statusapprove', [0, 1]);
            })
            ->where('exgroup', $this->id)
            ->get();

        return view('exports.group_excel', [
            'exgroup' => $exgroup,
            'expenses' => $expenses
        ]);
    }

    // public function drawings()
    // {
    //     $exgroup = Exgroup::find($this->id); // ดึงโดยตรงจาก id

    //     $expenses = Expense::with(['latestApprove'])
    //     ->whereHas('latestApprove', function ($query) {
    //         $query->where('typeapprove', 6)
    //             ->where('statusapprove', 1);
    //     })
    //         ->where('exgroup', $this->id)
    //         ->get();
    //     $baseRow = $expenses->count() + 14;

    //     $drawings = [];

    //     $signatures = [
    //         ['empid' => $exgroup->CreatedBy->empid, 'col' => 'G'],
    //         ['empid' => $exgroup->checkempid, 'col' => 'I'],
    //         ['empid' => $exgroup->finalempid, 'col' => 'L'],
    //     ];

    //     foreach ($signatures as $sig) {
    //         $file = Sigfile::where('empid', $sig['empid'])->value('path');
    //         if ($file && file_exists(storage_path("app/public/{$file}"))) {
    //             $drawing = new Drawing();
    //             $drawing->setName('Signature');
    //             $drawing->setPath(storage_path("app/public/{$file}"));
    //             $drawing->setHeight(100);
    //             $drawing->setCoordinates($sig['col'] . $baseRow);
    //             $drawings[] = $drawing;
    //         }
    //     }

    //     return $drawings;
    // }

     /** นับแถวตาม filter เดียวกับ view() */
    private function expensesCount(): int
    {
        return Expense::where('exgroup', $this->id)
            ->whereHas('latestApprove', function ($q) {
                $q->where('typeapprove', 6)
                  ->whereIn('statusapprove', [0, 1]); // ให้ตรงกับ view()
            })
            ->count();
    }

    /** แถวฐานของบล็อกลายเซ็น: header, SIGN, footer (สมมุติ 3 แถว) */
    private function baseRow(): int
    {
        // แถวสุดท้ายของตาราง + 1 (header ลายเซ็น) = +14 ตามเทมเพลตเดิมของคุณ
        return $this->expensesCount() + 12;
    }

    /** แถวที่ต้องวางรูป "ลงลายเซ็น" ให้อยู่กลางบล็อก */
    private function signRow(): int
    {
        return $this->baseRow() + 1; // row กลางของกรอบลายเซ็น
    }

    public function drawings()
    {
        $exgroup = Exgroup::with('CreatedBy')->findOrFail($this->id);

        // ความสูงของแถวลายเซ็น (หน่วย point) – ปรับตามขนาดกรอบใน template
        $signRowHeightPt = 100;     // เช่น 100pt (ค่อนข้างสูง)
        $signRowHeightPx = (int) round($signRowHeightPt * 96 / 72); // แปลง pt -> px
        $imgHeightPx     = max(10, $signRowHeightPx - 8); // ลบ margin 8px กันชนเส้นกรอบ

        $row = $this->signRow(); // วางรูปที่แถวลายเซ็น (แถวกลาง)

        $drawings = [];
        $signatures = [
            ['empid' => optional($exgroup->CreatedBy)->empid, 'col' => 'G'],
            ['empid' => $exgroup->checkempid,                 'col' => 'I'],
            ['empid' => $exgroup->finalempid,                 'col' => 'L'],
        ];

        foreach ($signatures as $sig) {
            if (!$sig['empid']) continue;

            $file = Sigfile::where('empid', $sig['empid'])->value('path');
            $full = $file ? storage_path("app/public/{$file}") : null;

            if ($full && file_exists($full)) {
                $d = new Drawing();
                $d->setName('Signature');
                $d->setPath($full);
                $d->setResizeProportional(true);
                $d->setHeight($imgHeightPx);                // สูงตามแถว (เผื่อ margin แล้ว)
                $d->setCoordinates($sig['col'] . $row);     // วางที่ "แถวลายเซ็น"
                $d->setOffsetY(4);                          // ดันลงเล็กน้อยให้อยู่กลางช่อง
                // หากต้องการจัดกึ่งกลางแนวนอน ลองเพิ่ม OffsetX ตามความกว้างช่อง merge
                // $d->setOffsetX(15);
                $drawings[] = $d;
            }
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        // style header เดิมของคุณ
        $styles = [
            'A5:Q5' => [
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

        // ตั้งความสูงแถวของ "แถวลายเซ็น" ให้สัมพันธ์กับรูป
        $signRow        = $this->signRow();  // แถวกลาง (สำหรับวางรูป)
        $signRowHeightPt = 100;              // ต้องเท่ากับที่ใช้คำนวณรูปด้านบน
        $sheet->getRowDimension($signRow)->setRowHeight($signRowHeightPt);

        // ถ้าบล็อกลายเซ็นมี 3 แถว: baseRow (หัว), signRow (ลายเซ็น), baseRow+2 (footer)
        // อาจตั้งความสูงหัว/ท้ายให้เล็กลงเพื่อเน้นช่องกลาง
        $sheet->getRowDimension($this->baseRow())->setRowHeight(18);       // header ของกรอบ
        $sheet->getRowDimension($this->baseRow() + 2)->setRowHeight(18);   // footer (เช่น "HR")

        // จัดแนวกลางในบล็อคลายเซ็น (ถ้าต้อง)
        foreach (['G','H','I','J','K','L','M'] as $col) {
            $sheet->getStyle("{$col}{$signRow}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        return $styles;
    }
}

