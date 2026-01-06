<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vbookingall;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CarBookingController extends Controller
{
    public function updateTime(Request $request, $bookid)
    {

        $request->validate([
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i'],
        ]);

        $booking = Booking::where('id', $bookid)->firstOrFail();

        // แปลงเวลา
        $start = Carbon::createFromFormat('H:i', $request->start_time);
        $end   = Carbon::createFromFormat('H:i', $request->end_time);

        //ถ้า end <= start → ถือว่าข้ามวัน
        if ($end->lte($start)) {
            $end->addDay();
        }


        $booking->departure_time = $start->format('H:i');
        $booking->return_time    = $end->format('H:i');
        $booking->save();

        return response()->json([
            'ok'      => true,
            'bookid'  => $bookid,
            'message' => "อัปเดตเวลา Booking {$bookid} เรียบร้อย",
        ]);
    }
}
