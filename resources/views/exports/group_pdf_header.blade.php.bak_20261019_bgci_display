<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face{
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path('fonts/THSarabunNew.ttf') }}') format('truetype');
        }
        @font-face{
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url('{{ public_path('fonts/THSarabunNew-Bold.ttf') }}') format('truetype');
        }
        body, table, td, b { font-family: 'THSarabunNew', sans-serif; font-size: 10pt; margin:0; }
    </style>
</head>
<body>
<table style="width:100%;">
    <tr>
        <td align="center">
            <b>สรุปรายชื่อพนักงาน เบิกค่าเดินทาง/เบี้ยเลี้ยง บริษัท {{ $exgroup->plantname }}</b>
        </td>
    </tr>
    <tr>
        <td align="center">
            ประจำสัปดาห์ {{ Thaidatenow(\Carbon\Carbon::parse($exgroup->groupdate)) }}
        </td>
    </tr>
</table>
</body>
</html>
