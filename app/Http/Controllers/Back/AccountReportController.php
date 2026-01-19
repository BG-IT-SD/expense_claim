<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Exgroup;
use App\Models\Vbookingall;
use App\Models\User;
use App\Models\GroupSpecial;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllowanceSummaryExport;
use App\Models\Plant;
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
        $search_bu = $request->input('search_bu');

        $bookIds = null;
        if ($search_plant) {
            $bookingQuery = Vbookingall::on('booking_carv2');
            $bookingQuery->where(function ($q) use ($search_plant) {
                $q->where('location_name', 'LIKE', "%{$search_plant}%")
                    ->orWhere('locationbu', 'LIKE', "%{$search_plant}%");
            });
            $bookIds = $bookingQuery->pluck('id');
        }

        $query = Expense::with([
            'exgroup',
            'userhr',
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

        // ค้นหาตามช่วงวันที่
        $query->whereHas('exgroup', function ($q) use ($start, $end) {
            if ($start && $end) {
                $q->whereBetween('paymentdate', [$start, $end]);
            }
        });

        // ค้นหาจาก empid
        if ($search_empid) {
            $query->where('empid', 'LIKE', "%{$search_empid}%");
        }

        //ค้นหาชื่อจาก users / group_specials
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

        //ค้นหาหน่วยงานจาก users / group_specials
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

        //ค้นหาสถานที่ (booking)
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
        $search_bu = $request->input('bu');

        $results = collect();

        $plants = Plant::where('status', 1)->where('deleted', 0)->get();

        return view('back.account.allowance_summary', compact(
            'results',
            'start',
            'end',
            'search_name',
            'search_plant',
            'search_department',
            'search_empid',
            'search_bu',
            'plants'
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

        if (!$request->anyFilled(['start_date', 'end_date', 'search_name', 'search_plant', 'search_department', 'search_empid', 'search_bu'])) {
            return response()->json([
                "draw"            => intval($draw),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => []
            ]);
        }

        $query = $this->getAllowanceQuery($request);
        $recordsTotal = $query->count();
        $recordsFiltered = $recordsTotal;

        $query->orderBy('exgroup', 'desc')
            ->orderBy($orderColumnName, $orderDir)
            ->skip($start)
            ->take($length);

        $data = $query->get();

        $formattedData = [];
        foreach ($data as $index => $row) {

            $payment_data = [
                '@data' => optional($row->finalApprove->exgroupRef)->id ?? null,
                'display' => optional($row->finalApprove->exgroupRef)->paymentdate
                    ? \Carbon\Carbon::parse($row->finalApprove->exgroupRef->paymentdate)->format('Y-m-d')
                    : '-'
            ];

            $total_travel = $row->travelexpenses + $row->gasolinecost;

            // ✅ ดึงชื่อและหน่วยงานตาม extype
            if ($row->extype == 1) {
                $fullname = optional($row->user)->fullname ?? '-';
                $dept     = optional($row->user)->dept ?? '-';
            } elseif (in_array($row->extype, [2, 3])) {
                $fullname = optional($row->groupSpecial)->fullname ?? '-';
                $dept     = optional($row->groupSpecial)->position ?? '-';
            } else {
                $fullname = '-';
                $dept = '-';
            }

            // --------- คำนวณ day_count ---------
            $booking = $row->extype == 2 ? $row->vbookingdrv : $row->vbooking;
            $startDate = optional($booking)->departure_date;
            $endDate   = null;

            if ($row->extype == 2) {
                // ใช้วันที่จาก expense_logs (ล่าสุด)
                $lastLog = $row->logs->sortByDesc('id')->first();

                if ($lastLog && preg_match('/\d{4}-\d{2}-\d{2}/', $lastLog->remark, $m)) {
                    $endDate = $m[0];
                } else {
                    $endDate = optional($booking)->return_date; // fallback
                }
            } else {
                $endDate = optional($booking)->return_date;
            }

            $dayCount = ($startDate && $endDate)
                ? \Carbon\Carbon::parse($startDate)
                ->diffInDays(\Carbon\Carbon::parse($endDate), true) + 1
                : '-';


            $formattedData[] = [
                'exid' => $row->id,
                'payment_date_display' => $payment_data,
                'DT_RowIndex' => $start + $index + 1,
                'vbooking_location' => optional($row->vbooking)->display_location ?? '-',
                'empid' => $row->empid,
                'user_fullname' => $fullname,
                'user_dept' => $dept,
                'user_level' => optional($row->userhr)->JOBGRADE_TITLE ?? '???',
                'departurefrom' => $startDate
                    ? \Carbon\Carbon::parse($startDate)->format('Y-m-d')
                    : '-',

                'returnfrom' => $endDate
                    ? \Carbon\Carbon::parse($endDate)->format('Y-m-d')
                    : '-',
                'day_count' => $dayCount,
                'costoffood' => number_format(round($row->costoffood), 2),
                'expresswaytoll' => number_format(round($row->expresswaytoll), 2),
                'travel_cost' => number_format(round($total_travel), 2),
                'totalprice' => number_format(round($row->totalprice), 2),
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
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $fileName = 'Allowance_Summary_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new AllowanceSummaryExport($request),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');

        $expenses = $this->getAllowanceQuery($request)->orderByDesc('id')->get();

        $fileName = 'Allowance_Summary_' . Carbon::now()->format('Y-m-d') . '.pdf';
        $lastGroupId = null;

        $pdf = Pdf::loadView('exports.allowance_summary_pdf', [
            'expenses' => $expenses,
            'start' => $request->input('start_date'),
            'end' => $request->input('end_date'),
            'lastGroupId' => $lastGroupId,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
}
