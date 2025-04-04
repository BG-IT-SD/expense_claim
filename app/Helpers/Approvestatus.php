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
            1 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-account-check"></span>อนุมัติจากหัวหน้า</span>',
            2 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-account-check"></span>ผู้จัดการส่วนตรวจสอบ</span>',
            3 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-account-check"></span>HRตรวจสอบข้อมูล</span>',
            4 => '<span class="badge bg-warning text-dark"><span class="mdi mdi-account-check"></span>อนุมัติจากฝ่ายHR</span>',
            default => '<span class="badge bg-warning text-dark">ประเภทไม่ระบุ</span>',
        };
    }
}
