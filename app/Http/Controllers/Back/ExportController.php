<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GroupExport;
use App\Models\Exgroup;
use App\Models\Expense;

class ExportController extends Controller
{
    public function exportGroupPdf($id)
    {
        $exgroup = Exgroup::findOrFail($id);
        $expenses = Expense::with(['vbooking', 'user']) // หรือ relation ที่คุณใช้
            ->where('exgroup', $id)
            ->get();

            $pdf = Pdf::loadView('exports.group_pdf', compact('exgroup', 'expenses'))
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'THSarabunNew');
        return $pdf->download("EXGROUP_{$id}.pdf");
    }



    public function exportGroupExcel($id)
    {
        $year = date('Y');
        return Excel::download(new GroupExport($id), "EXGROUP_{$id}_{$year}.xlsx");
    }
}
