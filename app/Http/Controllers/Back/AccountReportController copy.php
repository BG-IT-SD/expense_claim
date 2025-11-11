<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Exgroup;
use App\Models\Valldataemp;
use App\Models\Vbookingall;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllowanceSummaryExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountReportController extends Controller
{

    private function getAllowanceQuery(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        $search_name = $request->input('search_name');
        $search_plant = $request->input('search_plant');
        $search_department = $request->input('search_department');
        $search_empid = $request->input('search_empid');


        $empIds = null;
        if ($search_name || $search_department) {
            $userQuery = Valldataemp::on('mysql_hrms');
            if ($search_name) {

                $userQuery->where(function ($q) use ($search_name) {
                    $q->where('NAMFIRSTT', 'LIKE', "%{$search_name}%")
                      ->orWhere('NAMLASTENG', 'LIKE', "%{$search_name}%");
                });
            }
            if ($search_department) {
                $userQuery->where('DEPT', 'LIKE', "%{$search_department}%");
            }
            $empIds = $userQuery->pluck('CODEMPID');
        }

        $bookIds = null;
        if ($search_plant) {
            $bookingQuery = Vbookingall::on('booking_carv2');
            $bookingQuery->where(function ($q) use ($search_plant) {
                $q->where('location_name', 'LIKE', "%{$search_plant}%")
                  ->orWhere('locationbu', 'LIKE', "%{$search_plant}%");
            });
            $bookIds = $bookingQuery->pluck('id');
        }


        $query = Expense::with(['exgroup', 'userhr', 'finalApprove', 'vbooking'])
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


        $query->whereHas('exgroup', function ($q) use ($start, $end) {
            if ($start && $end) {
                $q->whereBetween('paymentdate', [$start, $end]);
            }
        });


        if ($search_empid) {
            $query->where('empid', 'LIKE', "%{$search_empid}%");
        }


        if ($empIds !== null) {
            $query->whereIn('empid', $empIds);
        }
        if ($bookIds !== null) {
            $query->whereIn('bookid', $bookIds);
        }

        return $query;
    }


    public function index(Request $request)
    {

        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        $search_name = $request->input('search_name');
        $search_plant = $request->input('search_plant');
        $search_department = $request->input('search_department');
        $search_empid = $request->input('search_empid');
        $results = collect();

        return view('back.account.allowance_summary', compact(
            'results', 'start', 'end',
            'search_name', 'search_plant', 'search_department', 'search_empid'
        ));
    }


    public function getReportData(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start');
        $length = $request->input('length');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumnName = $request->input('columns.' . $orderColumnIndex . '.name', 'id');
        $orderDir = $request->input('order.0.dir', 'desc');

        if (!$request->anyFilled(['start_date', 'end_date', 'search_name', 'search_plant', 'search_department', 'search_empid'])) {
             return response()->json([
                "draw"            => intval($draw), "recordsTotal"    => 0,
                "recordsFiltered" => 0, "data"            => []
            ]);
        }

        $query = $this->getAllowanceQuery($request);

        $recordsTotal = $query->count();
        $recordsFiltered = $recordsTotal;

        $query->orderBy('exgroup', 'desc') //เรียงตาม exgroup ก่อน
              ->orderBy($orderColumnName, $orderDir) // ค่อยเรียงตามที่ User กด
              ->skip($start)
              ->take($length);

        $data = $query->get();

        //จัดรูปแบบข้อมูล (Format Data)
        $formattedData = [];
        foreach ($data as $index => $row) {

            //ใช้ exgroup ที่ผูกมากับ Expense โดยตรง (ซึ่งตรงกับ orderBy)
            $payment_data = [
                '@data' => optional($row->finalApprove->exgroupRef)->id ?? null, //ID สำหรับ Grouping
                'display' => optional($row->finalApprove->exgroupRef)->paymentdate ? \Carbon\Carbon::parse($row->finalApprove->exgroupRef->paymentdate)->format('Y-m-d') : '-' // ⬅️ ข้อความสำหรับแสดงผล
            ];

            $total_travel = $row->travelexpenses + $row->gasolinecost;
            $formattedData[] = [
                'exid' => $row->id,
                'payment_date_display' => $payment_data,
                'DT_RowIndex' => $start + $index + 1,
                'vbooking_location' => optional($row->vbooking)->display_location ?? '-',
                'empid' => $row->empid,
                'user_fullname' => optional($row->userhr)->NAMFIRSTT . ' ' . optional($row->userhr)->NAMLASTT,
                'user_dept' => optional($row->userhr)->DEPT ?? '???',
                'user_level' => optional($row->userhr)->JOBGRADE_TITLE ?? '???',
                'departurefrom' =>  optional($row->vbooking)->departure_date ? \Carbon\Carbon::parse($row->vbooking->departure_date)->format('Y-m-d') : '-',
                'returnfrom' => optional($row->vbooking)->return_date ? \Carbon\Carbon::parse($row->vbooking->return_date)->format('Y-m-d') : '-',
                'day_count' => (optional($row->vbooking)->departure_date && optional($row->vbooking)->return_date) ? (\Carbon\Carbon::parse($row->vbooking->departure_date)->diffInDays(\Carbon\Carbon::parse($row->vbooking->return_date)) + 1) : '-',
                'costoffood' => number_format(round($row->costoffood) , 2),
                'expresswaytoll' => number_format(round($row->expresswaytoll), 2),
                'travel_cost' => number_format(round($total_travel) , 2),
                'totalprice' => number_format(round($row->totalprice) , 2),


                'user_company' => optional($row->finalApprove->exgroupRef)->plantname ?? '-'
            ];
        }


        return response()->json([
            "draw"            => intval($draw),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => $formattedData
        ]);
    }


  public function exportExcel(Request $request)
    {

        $fileName = 'Allowance_Summary_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new AllowanceSummaryExport($request),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $expenses = $this->getAllowanceQuery($request)->orderByDesc('id')->get();


        $fileName = 'Allowance_Summary_' . Carbon::now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('exports.allowance_summary_pdf', [
            'expenses' => $expenses,
            'start' => $request->input('start_date'),
            'end' => $request->input('end_date')
        ])->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
}
