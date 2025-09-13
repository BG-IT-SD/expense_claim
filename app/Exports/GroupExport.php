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

        // ความสูงรูปอิงแถวลายเซ็น (ต้องเท่ากับ styles())
        $signRowHeightPt = 60;
        $signRowHeightPx = (int) round($signRowHeightPt * 96 / 72);
        $imgHeightPx     = max(10, $signRowHeightPx - 10);

        $row = $this->signRow();

        // ใช้ 2 คอลัมน์ต่อช่อง เท่ากันทุกช่อง
        $boxes = [
            ['empid' => $exgroup->checkempid, 'anchorCol' => 'G', 'mergeCols' => ['G', 'H']],
            ['empid' => $exgroup->nextmpid,   'anchorCol' => 'I', 'mergeCols' => ['I', 'J']],
            ['empid' => $exgroup->finalempid, 'anchorCol' => 'K', 'mergeCols' => ['K', 'L']],
        ];

        // กำหนดความกว้างคอลัมน์ (Excel width units) ให้ตรงกับ styles()
        $colWidthMap = [
            'G' => 12,
            'H' => 12,
            'I' => 12,
            'J' => 12,
            'K' => 12,
            'L' => 12,
        ];

        $drawings = [];
        foreach ($boxes as $box) {
            if (!$box['empid']) continue;

            $rel  = \App\Models\Sigfile::where('empid', $box['empid'])->value('path');
            $full = $rel ? storage_path("app/public/{$rel}") : null;
            if (!$full || !file_exists($full)) continue;

            [$origW, $origH] = @getimagesize($full) ?: [0, 0];
            if ($origW <= 0 || $origH <= 0) continue;

            // กว้างกล่อง = ผลรวมความกว้าง 2 คอลัมน์ (approx: 7px ต่อ 1 หน่วย + 5px padding)
            $boxWpx = 0;
            foreach ($box['mergeCols'] as $col) {
                $w = $colWidthMap[$col] ?? 8.43;
                $boxWpx += (int) round($w * 7 + 5);
            }
            $boxWpx = max(10, $boxWpx - 8);

            // สเกลรูปตามความสูง และถ้ากว้างเกินกล่องให้หดตามความกว้าง
            $tH = $imgHeightPx;
            $tW = (int) floor(($origW / $origH) * $tH);
            if ($tW > $boxWpx) {
                $tW = $boxWpx;
                $tH = (int) floor(($origH / $origW) * $tW);
            }

            // จัดกึ่งกลาง
            $offsetX = max(0, (int) floor(($boxWpx - $tW) / 2));
            $offsetY = max(0, (int) floor(($signRowHeightPx - $tH) / 2));

            $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $d->setName('Signature');
            $d->setPath($full);
            $d->setResizeProportional(false);
            $d->setWidth($tW);
            $d->setHeight($tH);
            $d->setCoordinates($box['anchorCol'] . $row);
            $d->setOffsetX($offsetX);
            $d->setOffsetY($offsetY);

            $drawings[] = $d;
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

        // ให้ความสูงแถวลายเซ็นตรงกับ drawings()
        $signRow = $this->signRow();
        $sheet->getRowDimension($signRow)->setRowHeight(60); // pt

        // (ถ้าต้อง) ตั้งความกว้างคอลัมน์ให้ตรงกับที่ใช้คำนวณ
        foreach (['G', 'H', 'I', 'J', 'K', 'L'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // (ถ้า template ยังไม่ merge) ให้ merge 2 คอลัมน์ต่อกล่อง ทั้ง 3 แถว (หัว/ลายเซ็น/ฟุตเตอร์)
        $base = $this->baseRow();      // แถวหัวของกรอบลายเซ็น
        $sheet->mergeCells("G{$base}:H{$base}");
        $sheet->mergeCells("I{$base}:J{$base}");
        $sheet->mergeCells("K{$base}:L{$base}");

        $sheet->mergeCells("G{$signRow}:H{$signRow}");
        $sheet->mergeCells("I{$signRow}:J{$signRow}");
        $sheet->mergeCells("K{$signRow}:L{$signRow}");

        $footer = $base + 2;
        $sheet->mergeCells("G{$footer}:H{$footer}");
        $sheet->mergeCells("I{$footer}:J{$footer}");
        $sheet->mergeCells("K{$footer}:L{$footer}");

        // จัดกลางข้อความในแถวลายเซ็น
        foreach (['G', 'H', 'I', 'J', 'K', 'L'] as $col) {
            $sheet->getStyle("{$col}{$signRow}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }


        return $styles;
    }
}
