@php
    use Carbon\Carbon;
    $sum_morning = $sum_lunch = $sum_dinner = $sum_latenight = 0;
    $sum_food = $sum_gas = $sum_express = $sum_public = $sum_other = $sum_total = 0;
    $morning = 0;
    $lunch = 0;
    $dinner = 0;
    $latenight = 0;
@endphp

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr align="center" style="background-color: #D9D9D9; text-align: center;">
            <th rowspan="2" style="width: 100px;">Expense ID</th>
            <th rowspan="2" style="width: 100px;">สถานะอนุมัติ</th>
            <th rowspan="2" style="width: 120px;">ประเภทรถที่ใช้</th>
            <th rowspan="2" style="width: 80px;">สถานที่<br>ต้นทาง</th>
            <th rowspan="2" style="width: 80px;">สถานที่ไป<br>ปฏิบัติงาน</th>
            <th rowspan="2" style="width: 50px;">บริษัท</th>
            <th rowspan="2" style="width: 80px;">รหัสพนักงาน</th>
            <th rowspan="2" style="width: 120px;">ชื่อ – นามสกุล</th>
            <th rowspan="2" style="width: 100px;">หน่วยงาน</th>
            <th rowspan="2" style="width: 50px;">ระดับ</th>
            <th rowspan="2" style="width: 100px;">วันที่<br>เดินทางไป</th>
            <th rowspan="2" style="width: 100px;">เวลา<br>เดินทางไป</th>
            <th rowspan="2" style="width: 100px;">วันที่<br>เดินทางกลับ</th>
            <th rowspan="2" style="width: 100px;">เวลา<br>เดินทางกลับ</th>
            <th rowspan="2" style="width: 50px;">จำนวนวัน</th>

            <th colspan="4">1. ค่าเบี้ยเลี้ยง / อาหาร</th>
            <th colspan="2">2. ค่าน้ำมัน</th>
            <th rowspan="2">3. ค่าทางด่วน</th>
            <th rowspan="2">4. ค่ารถโดยสาร<br>สาธารณะ</th>
            <th rowspan="2">5. ค่าใช้จ่ายอื่นๆ</th>
            <th rowspan="2">Total</th>
        </tr>
        <tr align="center" style="background-color: #D9D9D9; text-align: center;">
            <th>เช้า</th>
            <th>กลางวัน</th>
            <th>เย็น</th>
            <th>ดึก</th>
            <th>ระยะทาง</th>
            <th>บาท</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($expenses as $i => $expense)
            @php
                $fullName =
                    $expense->extype == 2 || $expense->extype == 3
                        ? $expense->tech->fullname
                        : $expense->user->fullname;
                $booking = $expense->vbooking;

                $days = Carbon::parse($booking->departure_date)->diffInDays(Carbon::parse($booking->return_date)) + 1;

                $morning = $expense->foods->sum('meal1');
                $lunch = $expense->foods->sum('meal2');
                $dinner = $expense->foods->sum('meal3');
                $latenight = $expense->foods->sum('meal4');

                $food = $morning + $lunch + $dinner + $latenight;

                $gas = $expense->gasolinecost ?? 0;
                $distance = $expense->distance ?? 0;
                $express = $expense->expresswaytoll ?? 0;
                $public = $expense->publictransportfare ?? 0;
                $other = $expense->otherexpenses ?? 0;
                $total = $food + $gas + $express + $public + $other;

                $sum_morning += $morning;
                $sum_lunch += $lunch;
                $sum_dinner += $dinner;
                $sum_latenight += $latenight;

                $sum_food += $food;
                $sum_gas += $gas;
                $sum_express += $express;
                $sum_public += $public;
                $sum_other += $other;
                $sum_total += $total;
                $location = '';
                if ($booking->location == 12) {
                    $location = $booking->location_name;
                } else {
                    $location = $booking->locationbu;
                }
                // dd(BuEmp($expense->empid));
            @endphp
            <tr>
                {{-- <td align="center">{{ $i + 1 }}</td> --}}
                <td align="center">{{ 'EX' . $expense->id }}</td>
                <td>
                    @if (!is_null($expense->latestApprove->statusapprove))
                        {!! hr_status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                    @endif
                </td>
                <td>{{ $booking->title ?? '' }}</td>
                <td>{{ $booking->bu ?? '' }}</td>
                <td>{{ $location ?? '' }}</td>
                <td>{{ BuEmp($expense->empid) }} </td>
                <td>'{{ $expense->empid }}</td>
                <td>{{ $fullName }}</td>
                <td>{{ $expense->userhr->DEPT ?? '-' }}</td>
                <td align="center">{{ $expense->userhr->NUMLVL ?? '-' }}</td>
                <td align="center">{{ Carbon::parse($booking->departure_date)->format('d/m/Y') }}</td>
                <td align="center">{{ $booking->departure_time ?? '' }}</td>
                <td align="center">{{ Carbon::parse($booking->return_date)->format('d/m/Y') }}</td>
                <td align="center">{{ $booking->return_time ?? '' }}</td>
                <td align="center">{{ $days }}</td>

                <td align="right">{{ $morning }}</td>
                <td align="right">{{ $lunch }}</td>
                <td align="right">{{ $dinner }}</td>
                <td align="right">{{ $latenight }}</td>

                <td align="right">{{ $distance }}</td>
                <td align="right">{{ number_format($gas, 2) }}</td>
                <td align="right">{{ number_format($express, 2) }}</td>
                <td align="right">{{ number_format($public, 2) }}</td>
                <td align="right">{{ number_format($other, 2) }}</td>
                <td align="right"><strong>{{ number_format($total, 2) }}</strong></td>
            </tr>
        @endforeach
        <tr style="font-weight: bold;">
            <td colspan="15" align="center">Total</td>
            <td align="right">{{ $sum_morning }}</td>
            <td align="right">{{ $sum_lunch }}</td>
            <td align="right">{{ $sum_dinner }}</td>
            <td align="right">{{ $sum_latenight }}</td>
            <td align="right">{{ $distance }}</td>
            <td align="right">{{ number_format($sum_gas, 2) }}</td>
            <td align="right">{{ number_format($sum_express, 2) }}</td>
            <td align="right">{{ number_format($sum_public, 2) }}</td>
            <td align="right">{{ number_format($sum_other, 2) }}</td>
            <td align="right">{{ round($sum_total) }}</td>
        </tr>
    </tbody>
</table>
