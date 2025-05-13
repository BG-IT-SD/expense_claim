<table class="table table-bordered text-center">
    <thead class="table-secondary">
        <tr>
            <th>ลำดับ</th>
            <th>EXID</th>
            <th>สถานที่ไปปฏิบัติงาน</th>
            <th>บริษัท</th>
            <th>รหัสพนักงาน</th>
            <th>ชื่อ – นามสกุล</th>
            <th>หน่วยงาน</th>
            <th>ระดับ</th>
            <th>เลขบัญชี</th>
            <th>จากวันที่</th>
            <th>ถึงวันที่</th>
            <th>จำนวนวัน</th>
            <th>1. ค่าเบี้ยเลี้ยง / อาหาร</th>
            <th>2. ค่าน้ำมัน</th>
            <th>3. ค่าทางด่วน</th>
            <th>4. ค่ารถโดยสารสาธารณะ</th>
            <th>5. ค่าใช้จ่ายอื่นๆ</th>
            <th>Total (1+2+3)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sum_food = 0;
            $sum_gas = 0;
            $sum_express = 0;
            $sum_publictransport = 0;
            $sum_other = 0;
            $sum_total = 0;
        @endphp

        @foreach ($expenses as $i => $expense)
            @php
                $fullname =
                    $expense->extype == 2 || $expense->extype == 3
                        ? $expense->tech->fullname
                        : $expense->user->fullname;

                $days =
                    \Carbon\Carbon::parse(
                        $expense->vbooking->departure_date,
                    )->diffInDays(
                        \Carbon\Carbon::parse($expense->vbooking->return_date),
                    ) + 1;

                $food = $expense->costoffood ?? 0; //ค่าอาหาร
                $gas = $expense->gasolinecost ?? 0; // ค่าน้ำมัน
                $express = $expense->expresswaytoll ?? 0; //ค่าทางด่วน
                $publictransport = $expense->publictransportfare ?? 0; //ค่ารถสาธารณะ
                $other = $expense->otherexpenses ?? 0; // ค่าใช้จ่ายอื่นๆ
                $total = $food + $gas + $express + $publictransport + $other;

                $sum_food += $food; //ค่าอาหาร
                $sum_gas += $gas; // ค่าน้ำมัน
                $sum_express += $express; //ค่าทางด่วน
                $sum_publictransport += $publictransport; // ค่ารถสาธารณะ
                $sum_other += $other; // ค่าใช้จ่ายอื่นๆ
                $sum_total += $total;
                $sumtotalother = $sum_express + $sum_publictransport + $sum_other;
            @endphp
            <tr>
                <td>
                    {{ $i + 1 }}
                    <input type="hidden" name="expense_id[]" value="{{ $expense->id }}">

                </td>
                <td> {{ 'EX' . $expense->id }}</td>
                <td>{{ $expense->vbooking->locationbu }}</td>
                <td>{{ BuEmp($expense->empid) }}</td>
                <td>{{ $expense->empid }}</td>
                <td class="text-start">{{ $fullname }}</td>
                <td>{{ $expense->userhr->DEPT ?? '-' }}</td>
                <td>{{ $expense->userhr->NUMLVL ?? '-' }}</td>
                <td>{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($expense->vbooking->departure_date)->format('d/m/Y') }}
                </td>
                <td>{{ \Carbon\Carbon::parse($expense->vbooking->return_date)->format('d/m/Y') }}
                </td>
                <td>{{ $days }}</td>
                <td>{{ number_format($food, 2) }}</td>
                <td>{{ number_format($gas, 2) }}</td>
                <td>{{ number_format($express, 2) }}</td>
                <td>{{ number_format($publictransport, 2) }}</td>
                <td>{{ number_format($other, 2) }}</td>
                <td>{{ number_format($total, 2) }}</td>
            </tr>
        @endforeach

        <tr class="table-warning fw-bold">
            <td colspan="12">รวม</td>
            <td>{{ number_format($sum_food, 2) }}
            </td>
            <td>{{ number_format($sum_gas, 2) }}
            </td>
            <td>{{ number_format($sum_express, 2) }}
            </td>
            <td>{{ number_format($sum_publictransport, 2) }}
            </td>
            <td>{{ number_format($sum_other, 2) }}
            </td>
            <td>
                {{ number_format($sum_total, 2) }}
            </td>
        </tr>
    </tbody>
</table>