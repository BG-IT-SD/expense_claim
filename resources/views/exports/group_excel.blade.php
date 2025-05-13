<table>
    <thead>
        <tr>
            <th>ลำดับ</th>
            <th>ชื่อพนักงาน</th>
            <th>รหัส</th>
            <th>แผนก</th>
            <th>ค่าอาหาร</th>
            <th>ค่าน้ำมัน</th>
            <th>อื่น ๆ</th>
            <th>รวม</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->user->fullname }}</td>
                <td>{{ $row->empid }}</td>
                <td>{{ $row->user->department }}</td>
                <td>{{ number_format($row->costoffood, 2) }}</td>
                <td>{{ number_format($row->gasolinecost, 2) }}</td>
                <td>{{ number_format($row->otherexpenses ?? 0, 2) }}</td>
                <td>{{ number_format($row->totalprice, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
