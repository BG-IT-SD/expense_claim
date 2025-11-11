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
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class GroupExportFinal implements FromView, WithStyles, WithDrawings,WithColumnFormatting
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

        return view('exports.group_excel_final', [
            'exgroup' => $exgroup,
            'expenses' => $expenses
        ]);
    }



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

    private function baseRow(): int
    {
        return $this->totalRenderedRows() + 7;
    }

    private function signRow(): int
    {
        return $this->baseRow() + 1;
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
            // $offsetY = max(0, (int) floor(($signRowHeightPx - $tH) / 2));
            $offsetY = max(0, (int) floor(($signRowHeightPx - $tH) / 2) - 4);

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

        // $footer = $base + 2;
        // $sheet->mergeCells("G{$footer}:H{$footer}");
        // $sheet->mergeCells("I{$footer}:J{$footer}");
        // $sheet->mergeCells("K{$footer}:L{$footer}");
        $footer2 = $base + 3;
        $sheet->mergeCells("G{$footer2}:H{$footer2}");
        $sheet->mergeCells("I{$footer2}:J{$footer2}");
        $sheet->mergeCells("K{$footer2}:L{$footer2}");

        // จัดกลางข้อความในแถวลายเซ็น
        foreach (['G', 'H', 'I', 'J', 'K', 'L'] as $col) {
            $sheet->getStyle("{$col}{$signRow}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }


        return $styles;
    }
    public function columnFormats(): array
    {
        return [
            'L' => NumberFormat::FORMAT_NUMBER_00, // ค่าเบี้ยเลี้ยง / อาหาร
            'M' => NumberFormat::FORMAT_NUMBER_00, // ค่าน้ำมัน
            'N' => NumberFormat::FORMAT_NUMBER_00, // ค่าทางด่วน
            'O' => NumberFormat::FORMAT_NUMBER_00, // ค่ารถโดยสารสาธารณะ
            'P' => NumberFormat::FORMAT_NUMBER_00, // ค่าใช้จ่ายอื่นๆ
            'Q' => NumberFormat::FORMAT_NUMBER_00, // Total
        ];
    }

    /**
     * คำนวณจำนวนแถวทั้งหมดที่ใช้ในรายงานจริง
     * (รวม header, ข้อมูล, แถวรวมสถานที่, และแถวรวมทั้งหมด)
     */
    private function totalRenderedRows(): int
    {
        // ─────────────────────────────
        // 1. Header ส่วนบนของเอกสาร
        // ─────────────────────────────
        // 2 แถวแรก: ชื่อรายงาน + วันที่
        $headerTop = 2;

        // ─────────────────────────────
        // 2. Header ตาราง
        // ─────────────────────────────
        // 1 แถว (คอลัมน์ ลำดับ–Total)
        $headerTable = 1;

        // ─────────────────────────────
        // 3. จำนวนรายการพนักงานทั้งหมด
        // ─────────────────────────────
        $count = Expense::where('exgroup', $this->id)
            ->whereHas('latestApprove', function ($q) {
                $q->where('typeapprove', 6)
                    ->whereIn('statusapprove', [0, 1]);
            })
            ->count();

        // ─────────────────────────────
        // 4. จำนวนกลุ่มสถานที่
        // ─────────────────────────────
        // Blade ของคุณจะมี “หัวสถานที่” 1 แถว + “รวมสถานที่” 1 แถว ต่อกลุ่ม
        $groupCount = Expense::with('vbooking')
            ->where('exgroup', $this->id)
            ->whereHas('latestApprove', function ($q) {
                $q->where('typeapprove', 6)
                    ->whereIn('statusapprove', [0, 1]);
            })
            ->get()
            ->groupBy(function ($e) {
                $locationId = $e->vbooking->locationid ?? null;
                $locationBu = $e->vbooking->locationbu ?? null;
                return $locationId == 12
                    ? 'Other'
                    : ($locationBu ?: $e->vbooking->display_location ?? 'ไม่ระบุสถานที่');
            })
            ->count();

        // ─────────────────────────────
        // 5. รวมทั้งหมด
        // ─────────────────────────────
        // ต่อ group มี +2 (หัวสถานที่ + รวมสถานที่)
        // ส่วนท้ายของรายงานมีอีก +1 (แถว Total รวมทั้งหมด)
        $rows = $headerTop + $headerTable + $count + ($groupCount * 2) + 1;

        return $rows;
    }
}
