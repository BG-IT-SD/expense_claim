@php
    use Carbon\Carbon;
    $grand_food = $grand_gas = $grand_express = $grand_public = $grand_other = $grand_total = 0;
@endphp

<table>
    <tr>
        <td colspan="18" align="center"><strong>สรุปรายชื่อพนักงาน เบิกค่าเดินทาง/เบี้ยเลี้ยง บริษัท
                {{ $exgroup->plantname }}</strong></td>
    </tr>
    <tr>
        <td colspan="18" align="center">ประจำสัปดาห์ {{ Thaidatenow(Carbon::parse($exgroup->groupdate)) }}</td>
    </tr>
</table>
<br>

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr style="background-color: #D9D9D9; text-align: center; font-weight: bold; border: 1px solid #000;">
            <th style="width: 40px;">ลำดับ</th>
            <th style="width: 80px;">สถานที่ไป<br>ปฏิบัติงาน</th>
            <th style="width: 150px;">วัตถุประสงค์</th>
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
            <th style="width: 120px;">3. ค่าทางด่วน</th>
            <th style="width: 150px;">4. ค่ารถโดยสารสาธารณะ</th>
            <th style="width: 120px;">5. ค่าใช้จ่ายอื่นๆ</th>
            <th style="width: 120px;">Total</th>
        </tr>
    </thead>

    <tbody>
        @php
            $no = 1;
            $grand_food = $grand_gas = $grand_express = $grand_public = $grand_other = $grand_total = 0;

            // Group ตามสถานที่
            $grouped = $expenses->groupBy(function ($e) {
                $locationId = $e->vbooking->locationid ?? null;
                $locationBu = $e->vbooking->locationbu ?? null;
                return $locationId == 12
                    ? 'อื่นๆ'
                    : ($locationBu ?:
                        $e->vbooking->display_location ?? 'ไม่ระบุสถานที่');
            });

            // เรียงชื่อกลุ่ม (อังกฤษก่อน Other ท้าย)
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

        @foreach ($grouped as $groupName => $group)
            @php
                $sum_food = $sum_gas = $sum_express = $sum_public = $sum_other = $sum_total = 0;
            @endphp

            <tr style="background-color:#f2f2f2;">
                <td colspan="18" style="border:1px solid #000;"><strong>สถานที่ไปปฏิบัติงาน:
                        {{ $groupName }}</strong></td>
            </tr>

            @foreach ($group as $i => $expense)
                @php
                    $fullname = in_array($expense->extype, [2, 3])
                        ? $expense->tech->fullname
                        : $expense->user->fullname;

                    // เลือก booking
                    $booking = $expense->extype == 2 ? $expense->vbookingdrv : $expense->vbooking;

                    $startDate = optional($booking)->departure_date;
                    $endDate = null;

                    if ($expense->extype == 2) {
                        // ถ้ามี bookid ใน logs แนะนำกรองให้ตรง booking ด้วย
                        $logs = $expense->logs;
                        // $logs = $logs->where('bookid', optional($booking)->id); // <-- uncomment ถ้า logs มี bookid

                        $lastLog = $logs->sortByDesc('id')->first();

                        if ($lastLog && preg_match('/\d{4}-\d{2}-\d{2}/', $lastLog->remark, $m)) {
                            $endDate = $m[0];
                        } else {
                            $endDate = optional($booking)->return_date; // fallback
                        }
                    } else {
                        $endDate = optional($booking)->return_date;
                    }

                    $days =
                        $startDate && $endDate
                            ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate), true) + 1
                            : 0;

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
                    <td style="text-align: center;border: 1px solid #000">{{ $no++ }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->vbooking->display_location }}
                    </td>
                     <td style="text-align: center;border: 1px solid #000">
                        {{ $expense->vbooking->objname ? $expense->vbooking->objname: 'ไม่พบข้อมูล' }}
                        <br>
                        หมายเหตุ: {{$expense->vbooking->remark ? $expense->vbooking->remark: ' '}}
                    </td>
                    <td style="text-align: center;border: 1px solid #000">{{ BuEmp($expense->empid) }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->empid }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $fullname }}</td>
                    <td style="text-align: center;font-size: 10pt;border: 1px solid #000">
                        {{ $expense->userhr->DEPT ?? '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->userhr->JOBGRADE_TITLE ?? '-' }}
                    </td>
                    <td style="text-align: center;border: 1px solid #000">{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">
                        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">
                       {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;border: 1px solid #000">{{ $days ?: '-' }}</td>
                    <td style="text-align: right;border: 1px solid #000">{{ number_format($food, 2) }}</td>
                    <td style="text-align: right;border: 1px solid #000">{{ number_format($gas, 2) }}</td>
                    <td style="text-align: right;border: 1px solid #000">{{ number_format($express, 2) }}</td>
                    <td style="text-align: right;border: 1px solid #000">{{ number_format($public, 2) }}</td>
                    <td style="text-align: right;border: 1px solid #000">{{ number_format($other, 2) }}</td>
                    <td style="text-align: right;border: 1px solid #000">
                        <strong>{{ number_format(round($total), 2) }}</strong>
                    </td>
                </tr>
            @endforeach

            {{-- รวมต่อสถานที่ --}}
            <tr style="font-weight:bold;">
                <td colspan="12" style="text-align: center; vertical-align: middle; border:1px solid #000;">
                    <strong> รวมสถานที่ {{ $groupName }} </strong>
                </td>
                <td style="text-align: right; border:1px solid #000;">
                    <strong>{{ number_format(round($sum_food), 2) }}</strong></td>
                <td style="text-align: right; border:1px solid #000;">
                    <strong>{{ number_format(round($sum_gas), 2) }}</strong></td>
                <td style="text-align: right; border:1px solid #000;">
                    <strong>{{ number_format(round($sum_express), 2) }}</strong></td>
                <td style="text-align: right; border:1px solid #000;">
                    <strong>{{ number_format(round($sum_public), 2) }}</strong></td>
                <td style="text-align: right; border:1px solid #000;">
                    <strong>{{ number_format(round($sum_other), 2) }}</strong></td>
                <td style="text-align: right; border:1px solid #000;">
                    <strong>{{ number_format(round($sum_total), 2) }}</strong></td>
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
        <tr class="border: 1px solid #000;" style="background-color:#C9DAF8; font-weight:bold;">
            <td colspan="12"
                style="text-align: center; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>Total</strong>
            </td>
            <td style="text-align: right; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>{{ number_format(round($grand_food), 2) }}</strong>
            </td>
            <td style="text-align: right; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>{{ number_format(round($grand_gas), 2) }}</strong>
            </td>
            <td style="text-align: right; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>{{ number_format(round($grand_express), 2) }}</strong>
            </td>
            <td style="text-align: right; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>{{ number_format(round($grand_public), 2) }}</strong>
            </td>
            <td style="text-align: right; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>{{ number_format(round($grand_other), 2) }}</strong>
            </td>
            <td style="text-align: right; vertical-align: middle; text-decoration: underline; border: 1px solid #000;">
                <strong>{{ number_format(round($grand_total), 2) }}</strong>
            </td>
        </tr>
    </tbody>
</table>


<br><br><br>

<table style="width: 100%; border-collapse: collapse; text-align:center;">
    <tr>
        <td colspan="6"></td>
        <td colspan="2" style="border:1px solid #000;"><strong>ผู้ตรวจสอบ</strong></td>
        <td colspan="2" style="border:1px solid #000;"><strong>ผู้อนุมัติ</strong></td>
        <td colspan="2" style="border:1px solid #000;"><strong>ผู้อนุมัติ</strong></td>
        <td colspan="6"></td>
    </tr>
    <tr style="height: 80px;">
        <td colspan="6"></td>
        <td colspan="2" style="border:1px solid #000; height:80px;"></td>
        <td colspan="2" style="border:1px solid #000;"></td>
        <td colspan="2" style="border:1px solid #000;"></td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="2" style="border:1px solid #000;">{{ HRPosition($exgroup->checkempid) }}</td>
        <td colspan="2" style="border:1px solid #000;">{{ HRPosition($exgroup->nextmpid) }}</td>
        <td colspan="2" style="border:1px solid #000;">{{ HRPosition($exgroup->finalempid) }}</td>
        <td colspan="6"></td>
    </tr>
</table>
