<?php

use App\Models\Approve;

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
                2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่ตรวจสอบ</span>',
                default => '<span class="badge bg-secondary">สถานะไม่ทราบ</span>',
            };
        }

        // กรณีทั่วไป
        return match ($status) {
            0 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-clock-time-eight"></span>รออนุมัติ</span>',
            1 => '<span class="badge bg-success"><span class="mdi mdi-check-circle"></span>อนุมัติแล้ว</span>',
            2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่อนุมัติ</span>',
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
            5 => '<span class=""badge rounded-pill bg-label-success"><span class="mdi mdi-account-check"></span>อนุมัติจากผู้จัดการฝ่ายHR</span>',
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
            5 => '<span class=""badge rounded-pill bg-label-success"><span class="mdi mdi-account-check"></span>อนุมัติจากผู้จัดการฝ่ายHR</span>',
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
                2 => '<span class="badge bg-danger"><span class="mdi mdi-close-circle"></span>ไม่ตรวจสอบ</span>',
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
            default => '<span class="badge bg-secondary">สถานะไม่ทราบ</span>',
        };
    }
}
