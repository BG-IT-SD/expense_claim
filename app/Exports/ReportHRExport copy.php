<?php

namespace App\Exports;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportHRExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $start = Carbon::parse($this->request->exdate)->startOfDay();
        $end   = Carbon::parse($this->request->end_exdate)->endOfDay();

        $query = Expense::query()
            ->select([
                'id',
                'extype',
                'empid',
                'bookid',
                'created_at',
                'departuretime',
                'returntime',
                'totaldistance',
                'distancemore',
                'costoffood',
                'gasolinecost',
                'expresswaytoll',
                'publictransportfare',
                'otherexpenses',
                'totalprice',
                'departurefrom',
                'map_a_name',
                'exgroup',
            ])
            ->with([
                // ใช้ approves ชุดเดียวคำนวณ latest + สายอนุมัติ
                'approves:id,exid,typeapprove,statusapprove,approvename,remark',

                'user:empid,fullname,bu',
                'tech:empid,fullname,bu',
                'userhr:CODEMPID,DEPT,JOBGRADE_TITLE,NUMBANK',

                'vbooking:id,departure_date,return_date',
                'vbookingdrv:id,departure_date,return_date',

                // ห้ามใส่ display_location เพราะเป็น accessor
                'vbookingreport:id,bu,title,car_status,passengers,person_type,driver_name,departure_time,return_time,location_name,locationid,locationbu',

                //  ถ้า exgroupData เป็น belongsTo(Exgroup::class,'exgroup','id') ให้ใช้ id,paymentdate ได้
                'exgroupData:id,paymentdate',
            ])
            ->whereIn('extype', [1, 2, 3])
            ->where('deleted', 0)
            ->where('status', 1)
            ->whereBetween('created_at', [$start, $end]);

        // ✅ Filter BU
        if ($this->request->filled('bu')) {
            $bu = $this->request->bu;
            $query->where(function ($q) use ($bu) {
                $q->whereHas('user', fn($qq) => $qq->where('bu', $bu))
                    ->orWhereHas('tech', fn($qq) => $qq->where('bu', $bu));
            });
        }

        //  Filter Status (ถ้าคุณหมายถึง statusapprove ของ latest)
        // ถ้าหน้า status คือค่า approve status ให้เปิดส่วนนี้:
        if ($this->request->filled('status')) {
            $status = (int) $this->request->status;
            $query->whereHas('approves', fn($q) => $q->where('statusapprove', $status));
        }

        return $query->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'บริษัท',
            'รหัสพนักงาน',
            'ชื่อ - นามสกุล',
            'หน่วยงาน',
            'ระดับ',
            'เลขบัญชี',
            'Booking ID',
            'ประเภทรถ',
            'จำนวนผู้โดยสาร',
            'Status การเดินทาง',
            'ชื่อผู้ขับ',
            'สถานที่ต้นทาง',
            'สถานที่ไปปฏิบัติงาน',
            'จำนวนระยะทางที่ 1',
            'จำนวนระยะทางที่ 2',
            'รวมระยะทาง',
            'Expense ID',
            'จากวันที่',
            'เวลาออกเดินทาง',
            'ถึงวันที่',
            'เวลาที่ถึง',
            'จำนวนวัน',
            'ค่าเบี้ยเลี้ยง / อาหาร',
            'ค่าน้ำมัน',
            'ค่าทางด่วน',
            'ค่ารถโดยสารสาธารณะ',
            'ค่าใช้จ่ายอื่นๆ',
            'Total',
            'ประเภทอนุมัติ (ข้อความ)',
            'สถานะการอนุมัติ (ข้อความ)',
            'ผู้อนุมัติล่าสุด',
            'ผู้อนุมัติลำดับถัดไป',
            'หมายเหตุ',
            'วันที่จ่าย',
        ];
    }

    public function map($e): array
    {
        $fullname = in_array($e->extype, [2, 3])
            ? (optional($e->tech)->fullname ?? '-')
            : (optional($e->user)->fullname ?? '-');

        $company = in_array($e->extype, [2, 3])
            ? (optional($e->tech)->bu ?? '-')
            : (optional($e->user)->bu ?? '-');

        $booking = $e->extype == 2 ? $e->vbookingdrv : $e->vbooking;

        $carTitle  = optional($e->vbookingreport)->title;
        $carStatus = optional($e->vbookingreport)->car_status;

        $cartype = $carTitle
            ? $carTitle . (filled($carStatus) ? ' (' . $carStatus . ')' : '')
            : '-';

        $from = ($e->departurefrom == 2)
            ? ($e->map_a_name ?? '-')
            : (optional($e->vbookingreport)->bu ?? '-');

        $to = optional($e->vbookingreport)->display_location
            ?? (optional($e->vbookingreport)->location_name ?? '-');

        $distance1 = (float) ($e->totaldistance ?? 0);
        $distance2 = (float) ($e->distancemore ?? 0);
        $distanceTotal = $distance1 + $distance2;

        $startDate = optional($booking)->departure_date;
        $endDate   = optional($booking)->return_date;

        $days = 0;
        if ($startDate && $endDate) {
            $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate), true) + 1;
        }

        $departureTime = $e->departuretime ?? (optional($e->vbookingreport)->departure_time ?? '-');
        $returnTime    = $e->returntime ?? (optional($e->vbookingreport)->return_time ?? '-');

        // latest approve จาก approves
        $approves = $e->approves ?? collect();
        $latest   = $approves->sortByDesc('id')->first();

        $type   = optional($latest)->typeapprove;
        $status = optional($latest)->statusapprove;

        // สายอนุมัติ (คุณมีฟังก์ชันนี้อยู่แล้ว)
        $line = resolveApproveLineFromApprove($approves, (int) $e->extype);
        $approve_cur  = $line['approve_cur'] ?? '-';
        $approve_next = $line['approve_next'] ?? '-';

        // text-only (ฟังก์ชันที่คุณให้ทำ)
        $approveTypeText   = ($type !== null)   ? hr_type_approve_text_only($type, $status) : '-';
        $approveStatusText = ($status !== null) ? hr_status_approve_text_only($status, $type) : '-';

        // $food    = (float) ($e->costoffood ?? 0);
        // $gas     = (float) ($e->gasolinecost ?? 0);
        // $express = (float) ($e->expresswaytoll ?? 0);
        // $public  = (float) ($e->publictransportfare ?? 0);
        // $other   = (float) ($e->otherexpenses ?? 0);
        // $total   = round((float) ($e->totalprice ?? 0), 2);


        // $food    = number_format((float) ($e->costoffood ?? 0), 2);
        $food    = number_format(round($e->costoffood ?? 0), 2);
        $gas    = number_format(round($e->gasolinecost ?? 0), 2);
        $express    = number_format(round($e->expresswaytoll ?? 0), 2);
        $public    = number_format(round($e->publictransportfare ?? 0), 2);
        $other    = number_format(round($e->otherexpenses ?? 0), 2);
        $total    = number_format(round($e->totalprice ?? 0), 2);

        // $gas     =  number_format((float) ($e->gasolinecost ?? 0), 2);
        // $express = number_format((float) ($e->expresswaytoll ?? 0), 2);
        // $public  =  number_format((float) ($e->publictransportfare ?? 0), 2);
        // $other   = number_format((float) ($e->otherexpenses ?? 0), 2);
        // $total   = number_format(round((float) ($e->totalprice ?? 0)), 2);

        $paymentDate = optional($e->exgroupData)->paymentdate ?? '-';

        return [
            $company,
            $e->empid ?? '-',
            $fullname,
            optional($e->userhr)->DEPT ?? '-',
            optional($e->userhr)->JOBGRADE_TITLE ?? '-',
            optional($e->userhr)->NUMBANK ?? '-',
            $e->bookid ?? '-',
            $cartype,
            optional($e->vbookingreport)->passengers ?? 0,
            optional($e->vbookingreport)->person_type ?? '-',
            optional($e->vbookingreport)->driver_name ?? $fullname,
            $from,
            $to,
            number_format($distance1, 2),
            number_format($distance2, 2),
            number_format($distanceTotal, 2),
            'EX' . $e->id,
            $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '-',
            $departureTime,
            $endDate ? Carbon::parse($endDate)->format('d/m/Y') : '-',
            $returnTime,
            $days,

            $food,
            $gas,
            $express,
            $public,
            $other,
            $total,

            $approveTypeText,
            $approveStatusText,
            $approve_cur,
            $approve_next,
            optional($latest)->remark ?? '',
            $paymentDate,
        ];
    }

    public function columnFormats(): array
    {
        return [
            // ระยะทาง
            'N' => NumberFormat::FORMAT_NUMBER_00,
            'O' => NumberFormat::FORMAT_NUMBER_00,
            'P' => NumberFormat::FORMAT_NUMBER_00,

            // เงิน
            'W'  => NumberFormat::FORMAT_NUMBER_00, // #,##0.00
            'X'  => NumberFormat::FORMAT_NUMBER_00,
            'Y'  => NumberFormat::FORMAT_NUMBER_00,
            'Z'  => NumberFormat::FORMAT_NUMBER_00,
            'AA' => NumberFormat::FORMAT_NUMBER_00,
            'AB' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // ปรับได้ เช่น 500/1000/2000 ตามเครื่อง
    }
}
