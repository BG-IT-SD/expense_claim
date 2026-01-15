<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Plant;
use App\Models\Vbooking;
use Illuminate\Http\Request;

class ReportHRController extends Controller
{
    // public function index(Request $request)
    // {
    //     ini_set('max_execution_time', 600);
    //     ini_set('memory_limit', '1024M');


    //     $plants = Plant::where('status', 1)->where('deleted', 0)->get();
    //     $statusList = searchStatus();
    //     $expenses = collect(); // Collection ว่าง

    //     $month = request('month', now()->month);
    //     $year  = request('year', now()->year);


    //     // $bookIds = Vbooking::whereYear('departure_date', $year)
    //     //     ->whereMonth('departure_date', $month)
    //     //     ->pluck('id');
    //     // dd($bookIds);


    //     if ($request->filled('exdate') && $request->filled('end_exdate')) {

    //         $expenses = Expense::with(['latestApprove', 'vbooking', 'vbookingdrv', 'user', 'tech'])
    //             ->whereIn('extype', [1, 2, 3])
    //             ->where('deleted', 0)
    //             ->where('status', 1)
    //             ->whereBetween('created_at', [
    //                 $request->exdate,
    //                 $request->end_exdate
    //             ])
    //             // ->whereIn('bookid', $bookIds)
    //             ->get();
    //     }




    //     return view('back.hr.report.index', compact('plants', 'statusList', 'expenses'));
    // }

    public function index(Request $request)
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        $plants = Plant::where('status', 1)->where('deleted', 0)->get();
        $statusList = searchStatus();
        $expenses = collect();

        if ($request->filled('exdate') && $request->filled('end_exdate')) {

            $start = \Carbon\Carbon::parse($request->exdate)->startOfDay();
            $end   = \Carbon\Carbon::parse($request->end_exdate)->endOfDay();

            $expenses = Expense::with([
                'latestApprove',
                'approves',      // ✅ สำคัญ
                'vbooking',
                'vbookingdrv',
                'user',
                'tech',
                // 'exgroupData', // ✅ ถ้าจะใช้ $groups จากตรงนี้
            ])
                ->whereIn('extype', [1, 2, 3])
                ->where('deleted', 0)
                ->where('status', 1)
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $expenses->each(function ($expense) {
                $line = resolveApproveLineFromApprove($expense->approves, (int)$expense->extype);
                $expense->approve_cur  = $line['approve_cur'];
                $expense->approve_next = $line['approve_next'];
            });

            // ✅ คำนวณผู้อนุมัติถัดไป
            // $expenses->each(function ($expense) {

            //     // BU ที่จะส่งเข้า Approvestep (เลือกให้ตรงข้อมูลที่คุณใช้จริง)
            //     $bu = optional($expense->vbookingreport)->bu
            //         ?? optional($expense->user)->bu
            //         ?? optional($expense->tech)->bu;

            //     // group สำหรับ extype 2/3 (ถ้าระบบคุณใช้)
            //     $groups = $expense->exgroupData->groups ?? null; // <-- ปรับชื่อ field ให้ตรงจริง

            //     // step ปัจจุบัน = step ที่อนุมัติแล้วล่าสุด (statusapprove=1)
            //     $current = $expense->approves
            //         ->where('statusapprove', 1)
            //         ->max('typeapprove') ?? 0;

            //     $nextStep = $current + 1;

            //     // เก็บผลไว้ใน object เพื่อใช้ใน Blade
            //     $expense->nextApprover = Approvestep($bu, (int)$expense->extype, (int)$nextStep, $groups);
            // });
        }

        return view('back.hr.report.index', compact('plants', 'statusList', 'expenses'));
    }
}
