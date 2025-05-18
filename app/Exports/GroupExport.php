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

class GroupExport implements FromView, WithStyles,WithDrawings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $exgroup = Exgroup::findOrFail($this->id);

        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr'])
            ->where('exgroup', $this->id)
            ->get();

        return view('exports.group_excel', [
            'exgroup' => $exgroup,
            'expenses' => $expenses
        ]);
    }

    public function drawings()
    {
        $exgroup = Exgroup::find($this->id); // ดึงโดยตรงจาก id

        $expenses = Expense::where('exgroup', $this->id)->get();
        $baseRow = $expenses->count() + 13;

        $drawings = [];

        $signatures = [
            ['empid' => $exgroup->CreatedBy->empid, 'col' => 'G'],
            ['empid' => $exgroup->checkempid, 'col' => 'I'],
            ['empid' => $exgroup->finalempid, 'col' => 'L'],
        ];

        foreach ($signatures as $sig) {
            $file = Sigfile::where('empid', $sig['empid'])->value('path');
            if ($file && file_exists(storage_path("app/public/{$file}"))) {
                $drawing = new Drawing();
                $drawing->setName('Signature');
                $drawing->setPath(storage_path("app/public/{$file}"));
                $drawing->setHeight(100);
                $drawing->setCoordinates($sig['col'] . $baseRow);
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Header
            'A5:R5' => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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
}


