<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GroupExport;
use App\Models\Exgroup;
use App\Models\Expense;
use App\Models\Sigfile;

class ExportController extends Controller
{
    public function exportGroupPdf($id)
    {
        $exgroup = Exgroup::findOrFail($id);
        $expenses = Expense::with(['vbooking', 'user']) // หรือ relation ที่คุณใช้
            ->where('exgroup', $id)
            ->get();

               // หาลายเซ็นจาก empid
            $signatures = [
                'created' => Sigfile::where('empid', $exgroup->CreatedBy->empid)->value('path'),
                'checked' => Sigfile::where('empid', $exgroup->checkempid)->value('path'),
                'approved' => Sigfile::where('empid', $exgroup->finalempid)->value('path'),
            ];

            $exdate = $exgroup->groupdate;

            $pdf = Pdf::loadView('exports.group_pdf', compact('exgroup', 'expenses','signatures'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 0)
            ->setOption('defaultFont', 'THSarabunNew');
        return $pdf->download("EXGROUP_{$id}_{$exdate}.pdf");
    }



    public function exportGroupExcel($id)
    {
        $year = date('Y');
        return Excel::download(new GroupExport($id), "EXGROUP_{$id}_{$year}.xlsx");
    }
}
