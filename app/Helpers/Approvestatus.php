<?php

use App\Models\Approve;
use App\Models\ApproveStaff;
use App\Models\User;
use App\Models\Valldataemp;
use App\Models\ActivityLog;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

if (!function_exists('status_approve_badge')) {
    function status_approve_badge($status, $type = null)
    {
        $status = (int) $status;
        $type = (int) $type;

        // กรณี typeapprove == 2 || 3
        if ($type === 2 || $type === 3) {
            return match ($status) {
                0 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-clock-time-eight"></span>ยังไม่ตรวจสอบ</span>',
                1 => '<span class="badge bg-success"><span class="mdi mdi-check-circle"></span>ตรวจสอบแล้ว</span>',
                2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่ผ่านการตรวจสอบ</span>',
                9 => '<span class="badge bg-warning"><span class="mdi mdi-close-circle"></span>Hold</span>',
                99 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ยกเลิกโดยผู้ใช้</span>',
                default => '<span class="badge bg-secondary">สถานะไม่ทราบ</span>',
            };
        }

        // กรณีทั่วไป
        return match ($status) {
            0 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-clock-time-eight"></span>รออนุมัติ</span>',
            1 => '<span class="badge bg-success"><span class="mdi mdi-check-circle"></span>อนุมัติแล้ว</span>',
            2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่อนุมัติ</span>',
            9 => '<span class="badge bg-warning"><span class="mdi mdi-close-circle"></span>Hold</span>',
            99 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ยกเลิกโดยผู้ใช้</span>',
            default => '<span class="badge bg-secondary">สถานะไม่ทราบ</span>',
        };
    }
}

if (!function_exists('type_approve_text')) {
    function type_approve_text($type)
    {
        return match ((int) $type) {
            1 => '<span class="badge rounded-pill bg-label-secondary"><span class="mdi mdi-account-check"></span>อนุมัติจากหัวหน้า</span>',
            2 => '<span class="badge rounded-pill bg-label-warning"><span class="mdi mdi-account-check"></span>ผู้จัดการส่วนตรวจสอบ</span>',
            3 => '<span class="badge rounded-pill bg-label-info"><span class="mdi mdi-account-check"></span>HRตรวจสอบข้อมูล</span>',
            4 => '<span class="badge rounded-pill bg-label-primary"><span class="mdi mdi-account-check"></span>อนุมัติจากผู้จัดการส่วนHR</span>',
            5 => '<span class="badge rounded-pill bg-label-success"><span class="mdi mdi-account-check"></span>อนุมัติจากผู้จัดการฝ่ายHR</span>',
            6 => '<span class="badge rounded-pill bg-label-success"><span class="mdi mdi-account-check"></span>อนุมัติจากบัญชี</span>',
            default => '<span class="badge bg-warning text-dark">ประเภทไม่ระบุ</span>',
        };
    }
}

if (!function_exists('hr_type_approve_text')) {
    function hr_type_approve_text($type, $status = null)
    {
        $type = (int) $type;
        $status = (int) $status;

        // ✅ เงื่อนไขพิเศษ: หัวหน้าอนุมัติ และรอ HR ตรวจสอบ
        if ($type === 1 && $status === 1) {
            return '<span class="badge bg-info text-dark"><span class="mdi mdi-account-check"></span>รอ HR ตรวจสอบ</span>';
        }

        return match ($type) {
            1 => '<span class="badge rounded-pill bg-label-secondary"><span class="mdi mdi-account-check"></span>อนุมัติจากหัวหน้า</span>',
            2 => '<span class="badge rounded-pill bg-label-warning"><span class="mdi mdi-account-check"></span>ผู้จัดการส่วนตรวจสอบ</span>',
            3 => '<span class="badge rounded-pill bg-label-info"><span class="mdi mdi-account-check"></span>HRตรวจสอบข้อมูล</span>',
            4 => '<span class="badge rounded-pill bg-label-primary"><span class="mdi mdi-account-check"></span>อนุมัติจากผู้จัดการส่วนHR</span>',
            5 => '<span class="badge rounded-pill bg-label-success"><span class="mdi mdi-account-check"></span>อนุมัติจากผู้จัดการฝ่ายHR</span>',
            6 => '<span class="badge rounded-pill bg-label-success"><span class="mdi mdi-account-check"></span>อนุมัติจากบัญชี</span>',
            default => '<span class="badge bg-secondary">ประเภทไม่ระบุ</span>',
        };
    }
}

if (!function_exists('hr_status_approve_badge')) {
    function hr_status_approve_badge($status, $type = null)
    {
        $status = (int) $status;
        $type = (int) $type;

        // กรณี typeapprove == 2 || 3
        if ($type === 2 || $type === 3) {
            return match ($status) {
                0 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-clock-time-eight"></span>ยังไม่ตรวจสอบ</span>',
                1 => '<span class="badge bg-success"><span class="mdi mdi-check-circle"></span>ตรวจสอบแล้ว</span>',
                2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่ผ่านการตรวจสอบ</span>',
                9 => '<span class="badge bg-warning"><span class="mdi mdi-close-circle"></span>Hold</span>',
                99 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ยกเลิกโดยผู้ใช้</span>',
                default => '<span class="badge bg-secondary">สถานะไม่ทราบ</span>',
            };
        }
        if ($type === 1 && $status === 1) {
            return '<span class="badge bg-info text-dark"><span class="mdi mdi-timer-sand-complete"></span>รอHR ตรวจสอบ</span>';
        }

        // กรณีทั่วไป
        return match ($status) {
            0 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-clock-time-eight"></span>รออนุมัติ</span>',
            1 => '<span class="badge bg-success"><span class="mdi mdi-check-circle"></span>อนุมัติแล้ว</span>',
            2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่อนุมัติ</span>',
            9 => '<span class="badge bg-warning"><span class="mdi mdi-close-circle"></span>Hold</span>',
            99 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ยกเลิกโดยผู้ใช้</span>',
            default => '<span class="badge bg-secondary">สถานะไม่ทราบ</span>',
        };
    }
}

if (!function_exists('LevelEmp')) {
    function LevelEmp($empid)
    {
        $vAllemp = Valldataemp::where('CODEMPID', "$empid")->where('STAEMP', '!=', '9')->first();
        $level = $vAllemp?->NUMLVL ?? "";
        return  $level;
    }
}

if (!function_exists('BuEmp')) {
    function BuEmp($empid)
    {
        // $user = User::where('empid', "$empid")->where('status', 1)->where('deleted', 0)->first();
        $user = Valldataemp::where('CODEMPID', $empid)
            ->where('STAEMP', '!=', '9')
            ->first();
        $bu = $user?->alias_name ?? "";
        return  $bu;
    }
}

if (!function_exists('Approvestep')) {

    function Approvestep($bu, $type, $nextstep, $groups = null)
    {
        $step = "";
        $group = "";
        $email = "";
        $fullname = "";
        $empid = "";


        if ($type == 1) {

            if ($bu == 'BG' || $bu == 'BGE' || $bu == 'BGER' || $bu == 'BGA') {
                $group = 1;
                $step = $nextstep;
            } elseif ($bu == 'KBI' || $bu == 'BGCP') {
                $group = 2;
                $step = $nextstep;
            } elseif ($bu == 'PTI') {
                $group = 3;
                $step = $nextstep;
            } elseif ($bu == 'BGC') {
                // check BGC By BG Codeemp 011-010 | BGC
                $group = 2;
                $step = $nextstep;
            }
        } elseif ($type == 2) {
            $group = $groups;
            $step = $nextstep;
        } elseif ($type == 3) {
            $group = $groups;
            $step = $nextstep;
        }

        $nextApprove = ApproveStaff::where('extype', $type)
            ->where("group", $group)
            ->where("step", $step)
            ->where("deleted", 0)
            ->where("status", 1)
            ->first();

        $email = $nextApprove->email;
        $fullname = $nextApprove->fullname;
        $empid = $nextApprove->empid;


        return [
            "email" => $email,
            "fullname" => $fullname,
            "empid" => $empid,
        ];
    }
}

if (!function_exists('logAction')) {
    function logAction($action, $model = null, $description = null, $json = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'action'      => $action,
            'model'       => $model,
            'description' => $description,
            'json'        => $json,
            'user_id'     => $user?->empid ?? 'guest',
            'user_name'   => $user?->fullname ?? 'guest',
            'ip_address'  => Request::ip(),
            'url'         => Request::fullUrl(),
        ]);
    }
}

if (!function_exists('Thaidatenow')) {
    function Thaidatenow(Carbon $date)
    {
        $thaiMonths = [
            '01' => 'มกราคม',
            '02' => 'กุมภาพันธ์',
            '03' => 'มีนาคม',
            '04' => 'เมษายน',
            '05' => 'พฤษภาคม',
            '06' => 'มิถุนายน',
            '07' => 'กรกฎาคม',
            '08' => 'สิงหาคม',
            '09' => 'กันยายน',
            '10' => 'ตุลาคม',
            '11' => 'พฤศจิกายน',
            '12' => 'ธันวาคม',
        ];

        $day = $date->format('d');
        $month = $thaiMonths[$date->format('m')];
        $year = $date->year + 543;

        return "{$day} {$month} {$year}";
    }
}

if (!function_exists('EmailEmp')) {
    function EmailEmp($empid)
    {
        $user = Valldataemp::where('CODEMPID', $empid)
            ->where('STAEMP', '!=', '9')
            ->first();
        $email = $user?->EMAIL ?? "";
        return  $email;
    }
}

if (!function_exists('isApprover')) {
    function isApprover(): bool
    {
        return Approve::whereIn('typeapprove', [1, 2])
            ->where('empid', Auth::user()->empid)
            ->exists();
    }
}


if (!function_exists('hasReclaimedExpense')) {
    function hasReclaimedExpense($bookid, $empid, $currentExpenseId)
    {
        return Expense::where('bookid', $bookid)
            ->where('empid', $empid)
            ->where('id', '>', $currentExpenseId)
            ->exists();
    }
}