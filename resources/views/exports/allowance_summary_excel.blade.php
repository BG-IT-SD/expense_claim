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
            @foreach ($expenses as $loop => $r)
                @php
                    $currentGroupId = optional($r->finalApprove->exgroupRef)->id;
                    $total_travel = $r->travelexpenses + $r->gasolinecost;


                    if ($r->extype == 1) {
                        $fullname = optional($r->user)->fullname ?? '-';
                        $dept = optional($r->user)->dept ?? '-';
                    } elseif (in_array($r->extype, [2, 3])) {
                        $fullname = optional($r->groupSpecial)->fullname ?? '-';
                        $dept = optional($r->groupSpecial)->dept ?? '-';
                    } else {
                        $fullname = '-';
                        $dept = '-';
                    }
                @endphp


                @if ($lastGroupId !== null && $currentGroupId !== $lastGroupId)
                    <tr class="group-spacer">
                        <td colspan="15"></td>
                    </tr>
                @endif

                <tr>
                    <td>
                        {{ optional($r->finalApprove->exgroupRef)->paymentdate
                            ? \Carbon\Carbon::parse($r->finalApprove->exgroupRef->paymentdate)->format('Y-m-d')
                            : '-' }}
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($r->vbooking)->display_location ?? '-' }}</td>
                    <td>{{ $r->empid }}</td>
                    <td class="text-left">{{ $fullname }}</td>
                    <td>{{ $dept }}</td>
                    <td>{{ optional($r->userhr)->JOBGRADE_TITLE ?? '-' }}</td>
                    <td>
                        {{ optional($r->vbooking)->departure_date
                            ? \Carbon\Carbon::parse($r->vbooking->departure_date)->format('Y-m-d')
                            : '-' }}
                    </td>
                    <td>
                        {{ optional($r->vbooking)->return_date
                            ? \Carbon\Carbon::parse($r->vbooking->return_date)->format('Y-m-d')
                            : '-' }}
                    </td>
                    <td>
                        @if (optional($r->vbooking)->departure_date && optional($r->vbooking)->return_date)
                            {{ \Carbon\Carbon::parse($r->vbooking->return_date)->diffInDays(
                                \Carbon\Carbon::parse($r->vbooking->departure_date),
                            ) + 1 }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format(round($r->costoffood), 2) }}</td>
                    <td class="text-right">{{ number_format(round($r->expresswaytoll), 2) }}</td>
                    <td class="text-right">{{ number_format(round($total_travel), 2) }}</td>
                    <td class="text-right text-bold"><strong>{{ number_format(round($r->totalprice), 2) }}</strong></td>
                    <td>{{ optional($r->finalApprove->exgroupRef)->plantname ?? '-' }}</td>
                </tr>

            @php($lastGroupId = $currentGroupId)
            @endforeach


        </tbody>
    </table>

</body>

</html>
