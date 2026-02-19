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
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;



class ReportHRExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldAutoSize, WithEvents
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
                'approves:id,exid,typeapprove,statusapprove,approvename,remark',
                'logs:id,exid,remark,bookid',

                'user:empid,fullname,bu',
                'tech:empid,fullname,bu',
                'userhr:CODEMPID,DEPT,JOBGRADE_TITLE,NUMBANK',
                'vbooking:id,departure_date,return_date',
                'vbookingdrv:id,departure_date,return_date',
                'vbookingreport:id,bu,title,car_status,passengers,person_type,driver_name,departure_time,return_time,location_name,locationid,locationbu',
                'exgroupData:id,paymentdate',
            ])
            ->whereIn('extype', [1, 2, 3])
            ->where('deleted', 0)
            ->where('status', 1)
            ->whereBetween('created_at', [$start, $end]);

        // Filter BU
        if ($this->request->filled('bu')) {
            $bu = $this->request->bu;
            $query->where(function ($q) use ($bu) {
                $q->whereHas('user', fn($qq) => $qq->where('bu', $bu))
                    ->orWhereHas('tech', fn($qq) => $qq->where('bu', $bu));
            });
        }

        // Filter statusapprove
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

        $to = optional($e->vbookingreport)->location_name ?? '-';

        // $distance1 = (float) ($e->totaldistance ?? 0);
        // $distance2 = (float) ($e->distancemore ?? 0);

        $distance1    = is_numeric($e->totaldistance) ? (float)$e->totaldistance : 0;
        $distance2    = is_numeric($e->distancemore) ? (float)$e->distancemore : 0;
        $distanceTotal = $distance1 + $distance2;

        //   $booking = $e->extype == 2 ? $e->vbookingdrv : $e->vbooking;

        $startDate = optional($booking)->departure_date;
        $endDate = null;

        if ($e->extype == 2) {
            $logs = $e->logs ?? collect();

            // ถ้า logs มี bookid และอยากกรองให้ตรง booking จริง ๆ ให้เปิดบรรทัดนี้
            // $logs = $logs->where('bookid', optional($booking)->id);

            $lastLog = $logs->sortByDesc('id')->first();

            if ($lastLog && preg_match('/\d{4}-\d{2}-\d{2}/', (string)($lastLog->remark ?? ''), $m)) {
                $endDate = $m[0]; // yyyy-mm-dd จาก remark
            } else {
                $endDate = optional($booking)->return_date; // fallback
            }
        } else {
            $endDate = optional($booking)->return_date;
        }

        $days = ($startDate && $endDate)
            ? Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate), true) + 1
            : 0;
        $departureTime = $e->departuretime ?? (optional($e->vbookingreport)->departure_time ?? '-');
        $returnTime    = $e->returntime ?? (optional($e->vbookingreport)->return_time ?? '-');

        $approves = $e->approves ?? collect();
        $latest   = $approves->sortByDesc('id')->first();

        $type   = optional($latest)->typeapprove;
        $status = optional($latest)->statusapprove;

        $line = resolveApproveLineFromApprove($approves, (int)$e->extype);
        $approve_cur  = $line['approve_cur'] ?? '-';
        $approve_next = $line['approve_next'] ?? '-';

        $approveTypeText   = ($type !== null)   ? hr_type_approve_text_only($type, $status) : '-';
        $approveStatusText = ($status !== null) ? hr_status_approve_text_only($status, $type) : '-';

        // ✅ ส่งเป็นตัวเลขดิบ
        // $food    = (float) ($e->costoffood ?? 0);
        // $gas     = (float) ($e->gasolinecost ?? 0);
        // $express = (float) ($e->expresswaytoll ?? 0);
        // $public  = (float) ($e->publictransportfare ?? 0);
        // $other   = (float) ($e->otherexpenses ?? 0);
        // $total = (float) round((float) ($e->totalprice ?? 0), 0);   // 0 ตำแหน่ง = จำนวนเต็ม

        $food    = is_numeric($e->costoffood) ? (float)$e->costoffood : 0;
        $gas     = is_numeric($e->gasolinecost) ? (float)$e->gasolinecost : 0;
        $express = is_numeric($e->expresswaytoll) ? (float)$e->expresswaytoll : 0;
        $public  = is_numeric($e->publictransportfare) ? (float)$e->publictransportfare : 0;
        $other   = is_numeric($e->otherexpenses) ? (float)$e->otherexpenses : 0;
        $total   = round(is_numeric($e->totalprice) ? (float)$e->totalprice : 0, 0);




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
            (int) (optional($e->vbookingreport)->passengers ?? 0),
            optional($e->vbookingreport)->person_type ?? '-',
            optional($e->vbookingreport)->driver_name ?? $fullname,
            $from,
            $to,

            $distance1,
            $distance2,
            $distanceTotal,

            'EX' . $e->id,
            $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '-',
            $departureTime,
            $endDate ? Carbon::parse($endDate)->format('d/m/Y') : '-',
            $returnTime,
            (int)$days,

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

    // บังคับ format ด้วย “ลำดับคอลัมน์” (ไม่พังแม้เพิ่ม/ลดคอลัมน์)
    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function (AfterSheet $event) {

    //             $sheet = $event->sheet->getDelegate();
    //             $highestRow = $sheet->getHighestRow();

    //             if ($highestRow < 2) {
    //                 return; // ไม่มีข้อมูล
    //             }

    //             // 1) บังคับ format ระยะทาง N-O-P (14-16)
    //             foreach ([14, 15, 16] as $colIndex) {
    //                 $col = Coordinate::stringFromColumnIndex($colIndex);

    //                 $sheet->getStyle("{$col}2:{$col}{$highestRow}")
    //                     ->getNumberFormat()
    //                     ->setFormatCode('#,##0.00');
    //             }

    //             // 2) บังคับ format เงิน W-X-Y-Z-AA-AB (23-28)
    //             foreach ([23, 24, 25, 26, 27, 28] as $colIndex) {
    //                 $col = Coordinate::stringFromColumnIndex($colIndex);

    //                 $sheet->getStyle("{$col}2:{$col}{$highestRow}")
    //                     ->getNumberFormat()
    //                     ->setFormatCode('#,##0.00');

    //                 // ✅ กันช่องว่าง: ถ้า cell ว่าง ให้ set เป็น 0
    //                 for ($row = 2; $row <= $highestRow; $row++) {
    //                     $cell = "{$col}{$row}";
    //                     $val = $sheet->getCell($cell)->getValue();

    //                     if ($val === null || $val === '') {
    //                         $sheet->setCellValueExplicit($cell, 0, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
    //                     }
    //                 }
    //             }
    //         },
    //     ];
    // }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                if ($highestRow < 2) {
                    return;
                }

                /**
                 * ระยะทาง N-O-P (14-16)
                 */
                foreach ([14, 15, 16] as $colIndex) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);

                    // format
                    $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');

                    // ✅ เติม 0 ให้ cell ที่ว่าง
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $cell = "{$col}{$row}";
                        $val = $sheet->getCell($cell)->getValue();

                        if ($val === null || $val === '') {
                            $sheet->setCellValueExplicit(
                                $cell,
                                0,
                                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                            );
                        }
                    }
                }

                /**
                 * เงิน W-X-Y-Z-AA-AB (23-28)
                 */
                foreach ([23, 24, 25, 26, 27, 28] as $colIndex) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);

                    $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');

                    for ($row = 2; $row <= $highestRow; $row++) {
                        $cell = "{$col}{$row}";
                        $val = $sheet->getCell($cell)->getValue();

                        if ($val === null || $val === '') {
                            $sheet->setCellValueExplicit(
                                $cell,
                                0,
                                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                            );
                        }
                    }
                }
            },
        ];
    }




    public function chunkSize(): int
    {
        return 1000;
    }
}
