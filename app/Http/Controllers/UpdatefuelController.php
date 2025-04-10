<?php

namespace App\Http\Controllers;

use App\Models\FuelPrice91;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class UpdatefuelController extends Controller
{
    public function index()
    {
        // เรียก API ราคาน้ำมันจากบางจาก
        $response = Http::withOptions([
            'verify' => false,
        ])->get('https://oil-price.bangchak.co.th/ApiOilPrice2/th');
        if ($response->successful()) {
            $data = $response->json();
            $oilLists = json_decode($data[0]['OilList'], true); // แปลง OilList จาก JSON String เป็น Array

            $OilDateNow = $data[0]["OilDateNow"] ?? "";
            $OilPriceDate = $data[0]["OilPriceDate"]  ?? "";
            $OilPriceTime = $data[0]["OilPriceTime"]  ?? "";
            $OilMessageDate = $data[0]["OilMessageDate"]  ?? "";
            $OilMessageTime = $data[0]["OilMessageTime"]  ?? "";
            $OilRemark = $data[0]["OilRemark"]  ?? "";
            $OilRemark2 = $data[0]["OilRemark2"]  ?? "";
            echo 'OilDateNow : '.$OilDateNow.'<br>';
            echo 'OilRemark : '.$OilRemark2.'<br>';
            $latestPrice = FuelPrice91::where('deleted', 0)
                ->where('status', 1)
                ->orderByDesc('dateprice')
                ->first();
            $oillastprice = $latestPrice->price;
            if ($OilDateNow != "") {
                foreach ($oilLists as $key => $value) {
                    // echo $value["OilName"];
                    if ($value["OilName"] == 'แก๊สโซฮอล์ 91 S EVO') {
                        // PriceDifTomorrow
                        // PriceDifYesterday
                        // PriceToday
                        // PriceTomorrow
                        // PriceYesterday
                        if ($oillastprice != $value["PriceToday"]) {
                            $OilPriceDateData = Carbon::createFromFormat('d/m/Y', $OilDateNow)
                                ->subYears(543)
                                ->format('Y-m-d');
                            $exists = FuelPrice91::where('dateprice', $OilPriceDateData)->exists();

                            if (!$exists) {
                                $inserted = FuelPrice91::create([
                                    'dateprice' => $OilPriceDateData,
                                    'price' => $value['PriceToday'],
                                    'created_by' => 1,
                                    'status' => 1,
                                    'deleted' => 0,
                                ]);

                                if ($inserted) {
                                    $dataInsert = "บันทึกราคาน้ำมันเรียบร้อยแล้ว<br>";
                                    $dataInsert .= "date : ".$OilPriceDateData."<br>";
                                    $dataInsert .= "price : ".$value['PriceToday']."<br>";

                                    return $dataInsert;
                                    // ✅ บันทึกสำเร็จ
                                    // return response()->json([
                                    //     'status' => 200,
                                    //     'message' => 'บันทึกราคาน้ำมันเรียบร้อยแล้ว',
                                    //     'class' => 'success'
                                    // ]);
                                } else {
                                    // ❌ บันทึกล้มเหลว
                                    return 'เกิดข้อผิดพลาดในการบันทึก';
                                    // return response()->json([
                                    //     'status' => 500,
                                    //     'message' => 'เกิดข้อผิดพลาดในการบันทึก',
                                    //     'class' => 'error'
                                    // ]);
                                }
                            }else{
                                return 'มีข้อมูลของน้ำมันวันนี้แล้ว';
                            }
                        }else{
                            return 'ราคาน้ำมันไม่มีการอัพเดต';
                        }
                    }
                }
            }
        } else {
            return "no data";
        }
    }
}
