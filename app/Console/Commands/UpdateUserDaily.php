<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Valldataemp;
use App\Helpers\MailHelper; // ⚠️ อย่าลืม use Class ของ MailHelper ตาม Path จริงที่คุณใช้งาน

class UpdateUserDaily extends Command
{
    protected $signature = 'app:update-user-daily';
    protected $description = 'อัปเดตข้อมูล bu ของ User จาก v_alldataemp และส่งอีเมลแจ้งเตือน';

    public function handle()
    {
        $this->info('กำลังเริ่มตรวจสอบและอัปเดตข้อมูล BU...');

        $updatedCount = 0;

        User::chunk(500, function ($users) use (&$updatedCount) {

            $empIds = $users->pluck('empid')->filter()->toArray();

            $employees = Valldataemp::whereIn('CODEMPID', $empIds)
                                    ->get()
                                    ->keyBy('CODEMPID');

            foreach ($users as $user) {
                if ($employees->has($user->empid)) {
                    $employee = $employees->get($user->empid);

                    // ถ้า bu ไม่ตรงกัน ให้อัปเดตและบวกเลข counter
                    if ($user->bu !== $employee->alias_name) {
                        $user->update([
                            'bu' => $employee->alias_name
                        ]);

                        $updatedCount++;
                    }
                }
            }
        });

        // ---------------------------------------------------------
        // เมื่อการอัปเดตเสร็จสิ้นทั้งหมด ให้เรียกใช้ฟังก์ชันส่งอีเมล
        // ---------------------------------------------------------
        $mes = "ระบบได้ทำการตรวจสอบและอัปเดตข้อมูล BU ของ User เรียบร้อยแล้ว จำนวนที่อัปเดต: {$updatedCount} รายการ";
        $this->sendNotifyMail('แจ้งเตือน Update ข้อมูล User BU ประจำวัน', $mes);

        $this->info("เสร็จสิ้น! {$mes}");
    }

    /**
     * ฟังก์ชันสำหรับส่งอีเมลแจ้งเตือน
     */
    private function sendNotifyMail($title = 'แจ้งเตือนระบบ', $mes)
    {
        $data = [
            'type'         => 1,
            'title'        => $title,
            'mes'          => $mes,
        ];

        // เรียกใช้ MailHelper ตามโครงสร้างที่คุณมี
        MailHelper::sendExternalMail(
            'Kamolwan.b@bgiglass.com', // อีเมลผู้รับ
            'Expense System Update BU Notification',
            'mails.updateuser',
            $data,
            $title // Subject ของอีเมล
        );
    }
}
