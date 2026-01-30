<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>รายงานสรุปเบี้ยเลี้ยงประจำเดือน</title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        thead th {
            background-color: #D9D9D9;
            font-weight: bold;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .group-spacer td {
            border: none;
            height: 15px;
        }
    </style>
</head>

<body>

    <table border="1" style="border-collapse: collapse; width: 100%;">
        <thead>

            <tr>
                <th colspan="15" class="header-title">
                    รายงานสรุปเบี้ยเลี้ยงประจำเดือน
                </th>
            </tr>


            <tr style="background-color: #D9D9D9; text-align: center;">
                <th style="width: 100px;">รอบการจ่าย</th>
                <th style="width: 80px;">ลำดับที่</th>
                <th style="width: 150px;">สถานที่ไป<br>ปฏิบัติงาน</th>
                <th style="width: 120px;">รหัสพนักงาน</th>
                <th style="width: 180px;">ชื่อ – นามสกุล</th>
                <th style="width: 100px;">หน่วยงาน</th>
                <th style="width: 50px;">ระดับ</th>
                <th style="width: 80px;">จากวันที่</th>
                <th style="width: 80px;">ถึงวันที่</th>
                <th style="width: 80px;">จำนวนวัน</th>
                <th style="width: 150px;">จำนวนเงิน (ค่าเบี้ยเลี้ยง)</th>
                <th style="width: 120px;">ค่าทางด่วน</th>
                <th style="width: 120px;">จำนวนเงินเดินทาง</th>
                <th style="width: 150px;">Total</th>
                <th style="width: 120px;">ชื่อบริษัท</th>
            </tr>
        </thead>

        <tbody>
            @php
                // รวมทั้งหมด
                $grand_food = 0;
                $grand_express = 0;
                $grand_travel = 0;
                $grand_total = 0;

                // group ตามสถานที่ (เหมือนรายงานแรก)
                $grouped = $expenses->groupBy(function ($r) {
                    $locationId = optional($r->vbooking)->locationid;
                    $locationBu = optional($r->vbooking)->locationbu;

                    return $locationId == 12
                        ? 'อื่นๆ'
                        : ($locationBu ?:
                            optional($r->vbooking)->display_location ?? 'ไม่ระบุสถานที่');
                });

                // เรียงชื่อกลุ่ม (อื่นๆ ไว้ท้าย)
                $grouped = $grouped->sortKeysUsing(function ($a, $b) {
                    if ($a === 'อื่นๆ') {
                        return 1;
                    }
                    if ($b === 'อื่นๆ') {
                        return -1;
                    }
                    return strnatcasecmp($a, $b);
                });
            @endphp

            @foreach ($grouped as $groupName => $rows)
                @php
                    $sum_food = 0;
                    $sum_express = 0;
                    $sum_travel = 0;
                    $sum_total = 0;
                @endphp

                {{-- หัวกลุ่มสถานที่ --}}
                <tr style="background-color:#f2f2f2;">
                    <td colspan="15" class="text-left" style="font-weight:bold;">
                        สถานที่ไปปฏิบัติงาน: {{ $groupName }}
                    </td>
                </tr>

                @foreach ($rows as $idx => $r)
                    @php
                        $total_travel = ($r->travelexpenses ?? 0) + ($r->gasolinecost ?? 0);

                        if ($r->extype == 1) {
                            $fullname = optional($r->user)->fullname ?? '-';
                            $dept = optional($r->user)->dept ?? '-';
                        } elseif (in_array($r->extype, [2, 3])) {
                            $fullname = optional($r->groupSpecial)->fullname ?? '-';
                            $dept = optional($r->groupSpecial)->position ?? '-';
                        } else {
                            $fullname = '-';
                            $dept = '-';
                        }

                        // booking + dayCount (คง logic เดิม)
                        $booking = $r->extype == 2 ? $r->vbookingdrv : $r->vbooking;
                        $startDate = optional($booking)->departure_date;
                        $endDate = null;

                        if ($r->extype == 2) {
                            $lastLog = $r->logs->sortByDesc('id')->first();
                            if ($lastLog && preg_match('/\d{4}-\d{2}-\d{2}/', $lastLog->remark, $m)) {
                                $endDate = $m[0];
                            } else {
                                $endDate = optional($booking)->return_date;
                            }
                        } else {
                            $endDate = optional($booking)->return_date;
                        }

                        $dayCount =
                            $startDate && $endDate
                                ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate), true) +
                                    1
                                : null;

                        $food = $r->costoffood ?? 0;
                        $express = $r->expresswaytoll ?? 0;
                        $total = $r->totalprice ?? $food + $express + $total_travel;

                        // sum ต่อสถานที่
                        $sum_food += $food;
                        $sum_express += $express;
                        $sum_travel += $total_travel;
                        $sum_total += $total;
                    @endphp

                    <tr>
                        <td>
                            {{ optional($r->finalApprove->exgroupRef)->paymentdate
                                ? \Carbon\Carbon::parse($r->finalApprove->exgroupRef->paymentdate)->format('Y-m-d')
                                : '-' }}
                        </td>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ optional($r->vbooking)->display_location ?? '-' }}</td>
                        <td>{{ $r->empid }}</td>
                        <td class="text-left">{{ $fullname }}</td>
                        <td>{{ $dept }}</td>
                        <td>{{ optional($r->userhr)->JOBGRADE_TITLE ?? '-' }}</td>
                        <td>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('Y-m-d') : '-' }}</td>
                        <td>{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('Y-m-d') : '-' }}</td>
                        <td>{{ $dayCount ?? '-' }}</td>
                        <td class="text-right">{{ number_format(round($food), 2) }}</td>
                        <td class="text-right">{{ number_format(round($express), 2) }}</td>
                        <td class="text-right">{{ number_format(round($total_travel), 2) }}</td>
                        <td class="text-right"><strong>{{ number_format(round($total), 2) }}</strong></td>
                        <td>{{ optional($r->finalApprove->exgroupRef)->plantname ?? '-' }}</td>
                    </tr>
                @endforeach

                {{-- รวมต่อสถานที่ --}}
                <tr style="font-weight:bold;">
                    <td colspan="10" class="text-left">
                        รวมสถานที่ {{ $groupName }}
                    </td>
                    <td class="text-right">{{ number_format(round($sum_food), 2) }}</td>
                    <td class="text-right">{{ number_format(round($sum_express), 2) }}</td>
                    <td class="text-right">{{ number_format(round($sum_travel), 2) }}</td>
                    <td class="text-right">{{ number_format(round($sum_total), 2) }}</td>
                    <td></td>
                </tr>

                @php
                    $grand_food += $sum_food;
                    $grand_express += $sum_express;
                    $grand_travel += $sum_travel;
                    $grand_total += $sum_total;
                @endphp
            @endforeach

            {{-- รวมทั้งหมด --}}
            <tr style="background-color:#C9DAF8; font-weight:bold;">
                <td colspan="10" class="text-left"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format(round($grand_food), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format(round($grand_express), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format(round($grand_travel), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format(round($grand_total), 2) }}</strong></td>
                <td></td>
            </tr>
        </tbody>

    </table>

</body>

</html>
