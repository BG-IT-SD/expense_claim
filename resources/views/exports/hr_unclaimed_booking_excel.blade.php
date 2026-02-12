@php
    use Carbon\Carbon;
@endphp

<table>
    <tr>
        <td colspan="6" style="text-align:center;font-weight:bold;">
            รายงาน Booking ที่ยังเบิกไม่ครบ
        </td>
    </tr>
    <tr>
        <td colspan="6" style="text-align:center;">
            ช่วงวันที่:
            {{ $filters['exdate'] ?? '-' }} ถึง {{ $filters['end_exdate'] ?? '-' }}
        </td>
    </tr>
</table>

<br>

<table border="1" style="border-collapse:collapse;width:100%;">
    <thead>
        <tr style="background:#D9D9D9;font-weight:bold;text-align:center;">
            <th>NO.</th>
            <th>BOOKING ID</th>
            <th>RETURN_DATE</th>
            <th>EMPID (ที่ยังไม่เบิก)</th>
            <th>ROLE</th>
            <th>FULLNAME</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $r)
            <tr>
                <td style="text-align:center;">{{ $i+1 }}</td>
                <td style="text-align:center;">{{ $r['booking_id'] }}</td>
                <td style="text-align:center;">{{ $r['datetime_book'] }}</td>
                <td style="text-align:center;">{{ $r['empid'] }}</td>
                <td style="text-align:center;">{{ $r['role'] }}</td>
                <td style="text-align:left;">{{ $r['fullname'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
