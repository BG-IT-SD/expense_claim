<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path('fonts/THSarabunNew.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url('{{ public_path('fonts/THSarabunNew-Bold.ttf') }}') format('truetype');
        }

        body, table, th, tr, td, b {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    @php
        use Carbon\Carbon;
        $grand_food = $grand_gas = $grand_express = $grand_public = $grand_other = $grand_total = 0;
        $no = 1;

        // Group ตามสถานที่
        $grouped = $expenses->groupBy(function ($e) {
            $locationId = $e->vbooking->locationid ?? null;
            $locationBu = $e->vbooking->locationbu ?? null;
            return $locationId == 12
                ? 'อื่นๆ'
                : ($locationBu ?: $e->vbooking->display_location ?? 'ไม่ระบุสถานที่');
        });

        // เรียงชื่อกลุ่ม (อังกฤษก่อน Other ท้าย)
        $grouped = $grouped->sortKeysUsing(function ($a, $b) {
            if ($a === 'อื่นๆ') return 1;
            if ($b === 'อื่นๆ') return -1;
            return strnatcasecmp($a, $b);
        });
    @endphp

    <table style="width: 100%;">
        <tr>
            <td colspan="18" align="center">
                <b>สรุปรายชื่อพนักงาน เบิกค่าเดินทาง/เบี้ยเลี้ยง บริษัท {{ $exgroup->plantname }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="18" align="center">
                ประจำสัปดาห์ {{ Thaidatenow(Carbon::parse($exgroup->groupdate)) }}
            </td>
        </tr>
    </table>
    <br>

    <table border="1" style="border-collapse: collapse; width: 100%; table-layout: fixed;">
        <thead>
            <tr style="background-color: #D9D9D9; text-align: center;">
                <th style="width: 40px;">ลำดับ</th>
                <th style="width: 80px;">สถานที่ไป<br>ปฏิบัติงาน</th>
                <th style="width: 80px;">บริษัท</th>
                <th style="width: 120px;">รหัสพนักงาน</th>
                <th style="width: 180px;">ชื่อ – นามสกุล</th>
                <th style="width: 100px;">หน่วยงาน</th>
                <th style="width: 50px;">ระดับ</th>
                <th style="width: 100px;">เลขบัญชี</th>
                <th style="width: 80px;">จากวันที่</th>
                <th style="width: 80px;">ถึงวันที่</th>
                <th style="width: 80px;">จำนวนวัน</th>
                <th style="width: 150px;">1. ค่าเบี้ยเลี้ยง / อาหาร</th>
                <th style="width: 120px;">2. ค่าน้ำมัน</th>
                <th style="width: 120px;">3.ค่าทางด่วน</th>
                <th style="width: 150px;">4.ค่ารถโดยสารสาธารณะ</th>
                <th style="width: 120px;">5.ค่าใช้จ่ายอื่นๆ</th>
                <th style="width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grouped as $groupName => $group)
                @php
                    $sum_food = $sum_gas = $sum_express = $sum_public = $sum_other = $sum_total = 0;
                @endphp

                {{-- หัวสถานที่ --}}
                <tr style="background-color: #f2f2f2;">
                    <td colspan="17" style="border: 1px solid #000;">
                        <b>สถานที่ไปปฏิบัติงาน: {{ $groupName }}</b>
                    </td>
                </tr>

                @foreach ($group as $expense)
                    @php
                        $fullname =
                            $expense->extype == 2 || $expense->extype == 3
                                ? $expense->tech->fullname
                                : $expense->user->fullname;
                        $days =
                            Carbon::parse($expense->vbooking->departure_date)->diffInDays(
                                Carbon::parse($expense->vbooking->return_date),
                            ) + 1;
                        $food = $expense->costoffood ?? 0;
                        $gas = $expense->gasolinecost ?? 0;
                        $express = $expense->expresswaytoll ?? 0;
                        $public = $expense->publictransportfare ?? 0;
                        $other = $expense->otherexpenses ?? 0;
                        $total = $food + $gas + $express + $public + $other;

                        $sum_food += $food;
                        $sum_gas += $gas;
                        $sum_express += $express;
                        $sum_public += $public;
                        $sum_other += $other;
                        $sum_total += $total;
                    @endphp

                    <tr style="border: 1px solid #000;">
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td style="text-align: center;">{{ $expense->vbooking->display_location }}</td>
                        <td style="text-align: center;">{{ BuEmp($expense->empid) }}</td>
                        <td style="text-align: center;">{{ $expense->empid }}</td>
                        <td style="text-align: center;">{{ $fullname }}</td>
                        <td style="text-align: center; word-break: break-word;">
                            {{ $expense->userhr->DEPT ?? '-' }}</td>
                        <td style="text-align: center;">{{ $expense->userhr->JOBGRADE_TITLE ?? '-' }}</td>
                        <td style="text-align: center;">{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                        <td style="text-align: center;">
                            {{ Carbon::parse($expense->vbooking->departure_date)->format('d/m/Y') }}</td>
                        <td style="text-align: center;">
                            {{ Carbon::parse($expense->vbooking->return_date)->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ $days }}</td>
                        <td style="text-align: right;">{{ number_format($food, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($gas, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($express, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($public, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($other, 2) }}</td>
                        <td style="text-align: right;"><b>{{ number_format(round($total), 2) }}</b></td>
                    </tr>
                @endforeach

                {{-- รวมสถานที่ --}}
                <tr style="background-color:#E8E8E8; font-weight: bold;">
                    <td colspan="11" style="text-align: center;">
                        รวมสถานที่ {{ $groupName }}
                    </td>
                    <td style="text-align: right;">{{ number_format(round($sum_food), 2) }}</td>
                    <td style="text-align: right;">{{ number_format(round($sum_gas), 2) }}</td>
                    <td style="text-align: right;">{{ number_format(round($sum_express), 2) }}</td>
                    <td style="text-align: right;">{{ number_format(round($sum_public), 2) }}</td>
                    <td style="text-align: right;">{{ number_format(round($sum_other), 2) }}</td>
                    <td style="text-align: right;">{{ number_format(round($sum_total), 2) }}</td>
                </tr>

                @php
                    $grand_food += $sum_food;
                    $grand_gas += $sum_gas;
                    $grand_express += $sum_express;
                    $grand_public += $sum_public;
                    $grand_other += $sum_other;
                    $grand_total += $sum_total;
                @endphp
            @endforeach

            {{-- รวมทั้งหมด --}}
            <tr style="background-color:#C9DAF8; font-weight: bold;">
                <td colspan="11" style="text-align: center; text-decoration: underline;">
                    <b>Total</b>
                </td>
                <td style="text-align: right; text-decoration: underline;">{{ number_format(round($grand_food), 2) }}</td>
                <td style="text-align: right; text-decoration: underline;">{{ number_format(round($grand_gas), 2) }}</td>
                <td style="text-align: right; text-decoration: underline;">{{ number_format(round($grand_express), 2) }}</td>
                <td style="text-align: right; text-decoration: underline;">{{ number_format(round($grand_public), 2) }}</td>
                <td style="text-align: right; text-decoration: underline;">{{ number_format(round($grand_other), 2) }}</td>
                <td style="text-align: right; text-decoration: underline;">{{ number_format(round($grand_total), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <br><br><br>

    {{-- ส่วนลายเซ็น --}}
    <table style="width: 100%; table-layout: fixed;">
        <tbody>
            <tr>
                <td colspan="6"></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;"><b>ผู้ตรวจสอบ</b></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;"><b>ผู้อนุมัติ</b></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;"><b>ผู้อนุมัติ</b></td>
                <td colspan="6"></td>
            </tr>
            <tr style="height: 40px;">
                <td colspan="6"></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">
                    @if (!empty($signatures['checked']) && file_exists(public_path("storage/{$signatures['checked']}")))
                        <img src="{{ public_path("storage/{$signatures['checked']}") }}" style="height: 40px;">
                    @endif
                </td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">
                    @if (!empty($signatures['nextapproved']) && file_exists(public_path("storage/{$signatures['nextapproved']}")))
                        <img src="{{ public_path("storage/{$signatures['nextapproved']}") }}" style="height: 40px;">
                    @endif
                </td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">
                    @if (!empty($signatures['approved']) && file_exists(public_path("storage/{$signatures['approved']}")))
                        <img src="{{ public_path("storage/{$signatures['approved']}") }}" style="height: 40px;">
                    @endif
                </td>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6"></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">{{ HRPosition($exgroup->checkempid) }}</td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">{{ HRPosition($exgroup->nextmpid) }}</td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">{{ HRPosition($exgroup->finalempid) }}</td>
                <td colspan="6"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
