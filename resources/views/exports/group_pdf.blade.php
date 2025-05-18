
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 16px;
        }
    </style>
</head>
<body>
    @php
    use Carbon\Carbon;
    $sum_food = 0.00;
    $sum_gas = 0.00;
    $sum_express = 0.00;
    $sum_publictransport = 0.00;
    $sum_other = 0.00;
    $sum_total = 0.00;
@endphp

<table>
    <tr>
        <td colspan="18" align="center"><strong>สรุปรายชื่อพนักงาน เบิกค่าเดินทาง/เบี้ยเลี้ยง</strong></td>
    </tr>
    <tr>
        <td colspan="18" align="center">ประจำสัปดาห์ {{ Thaidatenow(Carbon::parse($exgroup->groupdate)) }}</td>
    </tr>
</table>
<br>
<table border="1" style="border-collapse: collapse; width: 100%;">
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
            <th style="width: 120px;">Total (1+2+3)</th>
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
                <td style="text-align: center;font-size: 10pt;border: 1px solid #000">{{ $expense->userhr->DEPT ?? '-' }}</td>
                <td style="text-align: center;border: 1px solid #000">{{ $expense->userhr->NUMLVL ?? '-' }}</td>
                <td style="text-align: center;border: 1px solid #000">{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                <td style="text-align: center;border: 1px solid #000">{{ Carbon::parse($expense->vbooking->departure_date)->format('d/m/Y') }}</td>
                <td style="text-align: center;border: 1px solid #000">{{ Carbon::parse($expense->vbooking->return_date)->format('d/m/Y') }}</td>
                <td style="text-align: center;border: 1px solid #000">{{ $days }}</td>
                <td style="text-align: center;border: 1px solid #000">="{{number_format($food, 2) }}"</td>
                <td style="text-align: center;border: 1px solid #000">="{{ number_format($gas, 2) }}"</td>
                <td style="text-align: center;border: 1px solid #000">="{{ number_format($express, 2) }}"</td>
                <td style="text-align: center;border: 1px solid #000">="{{ number_format($public, 2) }}"</td>
                <td style="text-align: center;border: 1px solid #000">="{{ number_format($other, 2) }}"</td>
                <td style="text-align: right;border: 1px solid #000;"><strong>="{{ number_format($total, 2) }}"</strong></td>
            </tr>
        @endforeach
        <tr class="border: 1px solid #000;">
            <td colspan="11" style="text-align: center; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>Total</strong></td>
            <td style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>="{{ number_format($sum_food, 2) }}"</strong></td>
            <td style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>="{{ number_format($sum_gas, 2) }}"</strong></td>
            <td  style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>="{{ number_format($sum_express, 2) }}"</strong></td>
            <td  style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>="{{ number_format($sum_publictransport, 2) }}"</strong></td>
            <td style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>="{{ number_format($sum_other, 2) }}"</strong></td>
            <td  style="text-align: right; vertical-align: middle;  text-decoration: underline;border: 1px solid #000;"><strong>="{{ number_format($sum_total, 2) }}"</strong></td>
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
        </tr>
        <tr>
            <td colspan="18"></td>
        </tr>

        <tr>
            <td colspan="6"></td>
            <td colspan="2" style="border: 1px solid #000; text-align: center;"><strong>ผู้จัดทำ</strong></td>
            <td colspan="2" style="border: 1px solid #000; text-align: center;"><strong>ผู้ตรวจสอบ</strong></td>
            <td colspan="2" style="border: 1px solid #000; text-align: center;"><strong>ผู้อนุมัติ</strong></td>
            <td colspan="6"></td>
        </tr>
        <tr style="height: 80px;">
            <td colspan="6"></td>
            <td colspan="2" style="border: 1px solid #000; height: 80px;"></td>
            <td colspan="2" style="border: 1px solid #000;"></td>
            <td colspan="2" style="border: 1px solid #000;"></td>
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



