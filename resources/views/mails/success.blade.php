<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Expense Claim Notification</title>
    <style>
        body {
            font-family: 'FC Iconic', sans-serif;
            background-color: #f9f9f9;
        }

        .email-wrapper {
            width: 100%;
            padding: 10px 0 30px 0;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
        }

        .email-header {
            background-color: #1f0aae;
            padding: 10px;
            border-bottom: 2px solid #c9c9c9;
            color: #fff;
            font-size: 16px;
            text-align: right;
        }

        .email-body {
            padding: 30px 10px 10px 50px;
            font-size: 16px;
            color: #1a1515;
        }

        .email-body strong {
            color: #160396;
        }

        .email-footer {
            background-color: #1f0aae;
            padding: 20px 50px;
        }

        a {
            color: #1f0aae;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <table class="email-container">
            <tr>
                <td class="email-header">
                    Expense Claim ระบบเบิกเบี้ยเลี้ยงและค่าเดินทาง
                </td>
            </tr>
            <tr>
                    <td class="email-body">
                        <p><strong>เรียน คุณ {{ $name }}</strong></p>
                        <p><strong>เรื่อง การอนุมัติการเบิกเลี้ยงและค่าเดินทาง</strong>
                        </p>
                        <p>เนื่องจาก การขออนุมัติการเบิกเบี้ยเลี้ยงและค่าเดินทางของ
                            คุณ {{ $full_name }} วันที่ {{ $departuredate }} ได้ถูกอนุมัติเรียบร้อยทุกขั้นตอนแล้ว</p>
                        <p>จึงเรียนมาเพื่อทราบ</p>
                        <br>
                        <p><strong>ขอแสดงความนับถือ</strong></p>
                    </td>
            </tr>
            <tr>
                <td class="email-footer">
                    &nbsp;
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
