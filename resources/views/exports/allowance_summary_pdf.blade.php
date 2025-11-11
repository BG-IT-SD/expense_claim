<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>รายงานสรุปเบี้ยเลี้ยงประจำเดือน</title>

    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url('{{ public_path('fonts/THSarabunNew.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'THSarabunNew';
            font-weight: bold;
            src: url('{{ public_path('fonts/THSarabunNew-Bold.ttf') }}') format('truetype');
        }

        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 12px;
            margin: 5px 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            background-color: #D9D9D9;
            font-weight: bold;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        th:nth-child(1),
        td:nth-child(1) {
            width: 7%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 5%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 10%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 7%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 12%;
        }

        th:nth-child(6),
        td:nth-child(6) {
            width: 10%;
        }

        th:nth-child(7),
        td:nth-child(7) {
            width: 5%;
        }

        th:nth-child(8),
        td:nth-child(8) {
            width: 6%;
        }

        th:nth-child(9),
        td:nth-child(9) {
            width: 6%;
        }

        th:nth-child(10),
        td:nth-child(10) {
            width: 5%;
        }

        th:nth-child(11),
        td:nth-child(11) {
            width: 8%;
        }

        th:nth-child(12),
        td:nth-child(12) {
            width: 6%;
        }

        th:nth-child(13),
        td:nth-child(13) {
            width: 6%;
        }

        th:nth-child(14),
        td:nth-child(14) {
            width: 7%;
        }

        th:nth-child(15),
        td:nth-child(15) {
            width: 5%;
        }

        .text-right {
            text-align: right;
        }

        .group-spacer td {
            border: none;
            height: 10px;
        }

        tr,
        td,
        th {
            page-break-inside: avoid !important;
        }
    </style>
</head>

<body>

    <table border="1">
        <thead>
            <tr>
                <th colspan="15" class="header-title">
                    <strong>รายงานสรุปเบี้ยเลี้ยงประจำเดือน</strong>
                </th>
            </tr>
            <tr>
                <th>รอบการจ่าย</th>
                <th>ลำดับที่</th>
                <th>สถานที่ไป<br>ปฏิบัติงาน</th>
                <th>รหัสพนักงาน</th>
                <th>ชื่อ – นามสกุล</th>
                <th>หน่วยงาน</th>
                <th>ระดับ</th>
                <th>จากวันที่</th>
                <th>ถึงวันที่</th>
                <th>จำนวนวัน</th>
                <th>จำนวนเงิน (ค่าเบี้ยเลี้ยง)</th>
                <th>ค่าทางด่วน</th>
                <th>จำนวนเงินเดินทาง</th>
                <th>Total</th>
                <th>ชื่อบริษัท</th>
            </tr>
        </thead>

        <tbody>

            @forelse($expenses as $loop => $r)
                @php
                    $currentGroupId = optional($r->finalApprove->exgroupRef)->id;
                    $total_travel = $r->travelexpenses + $r->gasolinecost;

                    // ใช้ชื่อและหน่วยงานตาม extype
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

                {{-- เว้นบรรทัดเมื่อเปลี่ยนรอบการจ่าย --}}
                @if ($lastGroupId !== null && $currentGroupId !== $lastGroupId)
                    <tr class="group-spacer">
                        <td colspan="15"></td>
                    </tr>
                @endif

                <tr>
                    <td>{{ optional($r->finalApprove->exgroupRef)->paymentdate ? \Carbon\Carbon::parse($r->finalApprove->exgroupRef->paymentdate)->format('Y-m-d') : '-' }}
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ optional($r->vbooking)->display_location ?? '-' }}</td>
                    <td>{{ $r->empid }}</td>
                    <td>{{ $fullname }}</td>
                    <td>{{ $dept }}</td>
                    <td>{{ optional($r->userhr)->JOBGRADE_TITLE ?? '-' }}</td>
                    <td>{{ optional($r->vbooking)->departure_date ? \Carbon\Carbon::parse($r->vbooking->departure_date)->format('Y-m-d') : '-' }}
                    </td>
                    <td>{{ optional($r->vbooking)->return_date ? \Carbon\Carbon::parse($r->vbooking->return_date)->format('Y-m-d') : '-' }}
                    </td>
                    <td>
                        @if (optional($r->vbooking)->departure_date && optional($r->vbooking)->return_date)
                            {{ \Carbon\Carbon::parse($r->vbooking->return_date)->diffInDays(\Carbon\Carbon::parse($r->vbooking->departure_date)) + 1 }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format(round($r->costoffood), 2) }}</td>
                    <td class="text-right">{{ number_format(round($r->expresswaytoll), 2) }}</td>
                    <td class="text-right">{{ number_format(round($total_travel), 2) }}</td>
                    <td class="text-right"><strong>{{ number_format(round($r->totalprice), 2) }}</strong></td>
                    <td>{{ optional($r->finalApprove->exgroupRef)->plantname ?? '-' }}</td>
                </tr>

                @php($lastGroupId = $currentGroupId)
            @empty
                <tr>
                    <td colspan="15">ไม่พบข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
