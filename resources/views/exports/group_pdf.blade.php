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

        body, table, th, tr ,td , b {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    @php
        use Carbon\Carbon;
        $sum_food = 0.0;
        $sum_gas = 0.0;
        $sum_express = 0.0;
        $sum_publictransport = 0.0;
        $sum_other = 0.0;
        $sum_total = 0.0;
    @endphp

    <table style="width: 100%;">
        <tr>
            <td colspan="18" align="center"><b>สรุปรายชื่อพนักงาน เบิกค่าเดินทาง/เบี้ยเลี้ยง</b></td>
        </tr>
        <tr>
            <td colspan="18" align="center">ประจำสัปดาห์ {{ Thaidatenow(Carbon::parse($exgroup->groupdate)) }}</td>
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
                <th style="width: 150px;word-break: break-word;">4.ค่ารถโดยสารสาธารณะ</th>
                <th style="width: 120px;">5.ค่าใช้จ่ายอื่นๆ</th>
                <th style="width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $i => $expense)
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
                    $sum_publictransport += $public;
                    $sum_other += $other;
                    $sum_total += $total;
                @endphp
                <tr style="border: 1px solid #000">
                    <td style="text-align: center;border: 1px solid #000">{{ $i + 1 }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->vbooking->locationbu }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ BuEmp($expense->empid) }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->empid }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $fullname }}</td>
                    <td style="text-align: center;word-break: break-word;border: 1px solid #000">
                        {{ $expense->userhr->DEPT ?? '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->userhr->NUMLVL ?? '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">
                        {{ Carbon::parse($expense->vbooking->departure_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center;border: 1px solid #000">
                        {{ Carbon::parse($expense->vbooking->return_date)->format('d/m/Y') }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $days }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ number_format($food, 2) }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ number_format($gas, 2) }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ number_format($express, 2) }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ number_format($public, 2) }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ number_format($other, 2) }}</td>
                    <td style="text-align: right;border: 1px solid #000;">
                        <b>{{ number_format($total, 2) }}</b></td>
                </tr>
            @endforeach
            <tr class="border: 1px solid #000;">
                <td colspan="11"
                    style="text-align: center; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>Total</b></td>
                <td
                    style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>{{ number_format($sum_food, 2) }}</b></td>
                <td
                    style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>{{ number_format($sum_gas, 2) }}</b></td>
                <td
                    style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>{{ number_format($sum_express, 2) }}</b></td>
                <td
                    style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>{{ number_format($sum_publictransport, 2) }}</b></td>
                <td
                    style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>{{ number_format($sum_other, 2) }}</b></td>
                <td
                    style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;">
                    <b>{{ number_format($sum_total, 2) }}</b></td>
            </tr>
            {{-- <tr>
            <td colspan="18"></td>
        </tr>
        <tr>
            <td colspan="18"></td>
        </tr>
        <tr>
            <td colspan="18"></td>
        </tr>
        <tr>
            <td colspan="18"></td>
        </tr>
        <tr>
            <td colspan="18"></td>
        </tr> --}}





        </tbody>
    </table>
    <br>
    <br>
    <br>
    <table style=" width: 100%; table-layout: fixed;">
        <tbody>
            <tr>
                <td colspan="6"></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;"><b>ผู้จัดทำ</b></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;"><b>ผู้ตรวจสอบ</b></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;"><b>ผู้อนุมัติ</b></td>
                <td colspan="6"></td>
            </tr>
            <tr style="height: 40px;">
                <td colspan="6"></td>
                <td colspan="2" style="border: 1px solid #000; height: 40px; text-align: center;">
                    @if (!empty($signatures['created']) && file_exists(public_path("storage/{$signatures['created']}")))
                        <img src="{{ public_path("storage/{$signatures['created']}") }}" style="height: 40px;">
                    @endif
                </td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">
                    @if (!empty($signatures['checked']) && file_exists(public_path("storage/{$signatures['checked']}")))
                        <img src="{{ public_path("storage/{$signatures['checked']}") }}" style="height: 40px;">
                    @endif
                </td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">
                    @if (!empty($signatures['approved']) && file_exists(public_path("storage/{$signatures['approved']}") ))
                        <img src="{{ public_path("storage/{$signatures['approved']}") }}" style="height: 40px;">
                    @endif
                </td>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6"></td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">HR</td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">HR</td>
                <td colspan="2" style="border: 1px solid #000; text-align: center;">HR</td>
                <td colspan="6"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
