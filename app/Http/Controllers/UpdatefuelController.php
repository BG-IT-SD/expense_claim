<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Models\FuelPrice91;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;



class UpdatefuelController extends Controller
{
    public function index()
    {
        // เรียก API ราคาน้ำมันจากบางจาก
        $response = Http::withOptions(['verify' => false])
            ->get('https://oil-price.bangchak.co.th/ApiOilPrice2/th');

        if (!$response->successful()) {
            return "no data";
        }

        $data = $response->json();
        $oilLists = json_decode($data[0]['OilList'], true);

        $OilDateNow = $data[0]["OilDateNow"] ?? "";
        $OilRemark2 = $data[0]["OilRemark2"] ?? "";

        echo 'OilDateNow : ' . $OilDateNow . '<br>';
        echo 'OilRemark : ' . $OilRemark2 . '<br>';

        $latestPrice = FuelPrice91::where('deleted', 0)
            ->where('status', 1)
            ->orderByDesc('dateprice')
            ->first();

        $oillastprice = $latestPrice->price ?? null;

        if (empty($OilDateNow)) {
            return "no OilDateNow";
        }

        foreach ($oilLists as $value) {
            if ($value["OilName"] !== 'แก๊สโซฮอล์ 91 S EVO') {
                continue;
            }

            $oilPriceToday = $value["PriceToday"];
            if ($oillastprice != $oilPriceToday) {
                $OilPriceDateData = Carbon::createFromFormat('d/m/Y', $OilDateNow)
                    ->subYears(543)
                    ->format('Y-m-d');

                $exists = FuelPrice91::where('dateprice', $OilPriceDateData)->exists();

                if (!$exists) {
                    $inserted = FuelPrice91::create([
                        'dateprice' => $OilPriceDateData,
                        'price'     => $oilPriceToday,
                        'created_by' => 1,
                        'status'    => 1,
                        'deleted'   => 0,
                    ]);

                    $resultMsg = "บันทึกราคาน้ำมันเรียบร้อยแล้ว<br>";
                    $resultMsg .= "date : $OilPriceDateData<br>";
                    $resultMsg .= "price : $oilPriceToday<br>";

                    $this->sendNotifyMail('แจ้งเตือน Update น้ำมัน', $resultMsg);
                    return $resultMsg;
                } else {
                    $this->sendNotifyMail('แจ้งเตือน Update น้ำมัน', 'มีข้อมูลของน้ำมันวันนี้แล้ว');
                    return 'มีข้อมูลของน้ำมันวันนี้แล้ว';
                }
            } else {
                $this->sendNotifyMail('แจ้งเตือน Update น้ำมัน', 'ราคาน้ำมันไม่มีการอัพเดต');
                return 'ราคาน้ำมันไม่มีการอัพเดต';
            }
        }
        // ถ้าไม่มี OilName ที่ต้องการ
        return 'ไม่มีข้อมูลแก๊สโซฮอล์ 91 S EVO';
    }

    private function sendNotifyMail($title = 'แจ้งเตือน Update น้ำมัน', $mes)
    {
        $data = [
            'type'         => 1,
            'title'        => $title,
            'mes'          => $mes,
        ];

        MailHelper::sendExternalMail(
            'Kamolwan.b@bgiglass.com',
            'Update ราคาน้ำมัน',
            'mails.updatefuel', // ชื่อ blade view
            $data,
            'Update ราคาน้ำมัน 91 S EVO '
        );
    }
}
