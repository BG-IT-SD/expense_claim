<?php

namespace App\Http\Controllers\Front;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\ApproveGroup;
use App\Models\DistanceRate;
use App\Models\Groupplant;
use App\Models\GroupSpecial;
use App\Models\Heademp;
use App\Models\Plant;
use App\Models\Valldataemp;
use App\Models\Vbooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApproveNotification;
use App\Mail\TestMail;
use App\Models\Approve;
use App\Models\Expense;
use App\Models\ExpenseFile;
use App\Models\ExpenseFood;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ข้อมูลจองรถตาม CODEMPID 7 วันย้อนหลัง
        // create view ที่รวมเป็นผู้โดยสารด้วย
        $empid = Auth::user()->empid;
        // $booking = Vbooking::where('booking_emp_id', "$empid")
        //     ->orWhere('passenger_empid', "$empid")
        //     ->get()
        //     ->unique('id')
        //     ->values();
        $booking = Vbooking::with([
            'expense' => function ($q) use ($empid) {
                $q->where('empid', $empid)
                    ->with('latestApprove'); // ✅ include latest approve
            }
        ])
            ->where(function ($q) use ($empid) {
                $q->where('booking_emp_id', $empid)
                    ->orWhere('passenger_empid', $empid);
            })
            ->get()
            ->unique('id')
            ->values();
        // dd($booking[0]->expense->id ?? 'no expense');
        return view('front.expenses.list', compact('booking'));
    }

    public function history()
    {
        $currentEmpid = Auth::user()->empid;
        $expenses = Expense::with(['latestApprove', 'vbooking', 'user'])
            ->where('empid', $currentEmpid)
            ->whereHas('latestApprove', function ($query) {
                $query->whereIn('typeapprove', [1, 2, 3, 4, 5]);
            })
            ->get();

        return view('front.expenses.history', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $booking = Vbooking::find($id);
        $empid = Auth::user()->empid;
        $empemail = Auth::user()->email;
        $empfullname = Auth::user()->fullname;
        // $booking->booking_emp_id ?? "";
        // MailHelper::SendMail('kamolwan.b@bgiglass.com', 'Subject from API', 'Some body text', 'dddd');


        $typegroup = 1;
        $totalDistance = 0;
        $startplant = "";
        $endplant = "";
        $PlantStart = $booking->bu;
        $bu = Auth::user()->bu;
        $level = 1;
        $empLevel = $this->LevelEmp($empid);
        // ession('level')
        if ($empLevel <= 7) {
            $level = 1;
        } else {
            $level = 2;
        }
        // เช็คประเภทuser
        $groupspecial = GroupSpecial::where('empid', $empid)->get();
        if ($groupspecial->isNotEmpty()) {
            if ($groupspecial->id == 1 || $groupspecial->id == 2) {
                $typegroup = 2;
            } else {
                $typegroup = 3;
            }
        }
        // Plant
        $plants = Plant::where('status', 1)->where('deleted', 0)
            // ->whereIn('id', [2, 4, 7, 9, 10, 11])
            ->get();

        // Databooking
        $departure_date = $booking->departure_date ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i') : null;
        $return_date = $booking->return_date ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i') : null;
        $reasons = ['อบรม', 'สัมมนา', 'ฝึกงาน', 'ติดตั้งเครื่องจักร', 'ลูกค้าร้องเรียน', 'พบลูกค้า', 'อื่นๆ'];

        if ($booking->locationid != 12) {
            $startplant = $PlantStart;
            $endplant = $booking->locationbu;
            $totalDistance = $this->getDistance($startplant, $endplant);
            // dd($totalDistance);
        }

        // Food
        $groupplant = Groupplant::with([
            'plant',
            'meal.group',
            'meal',
        ])
            ->where('deleted', 0)
            ->whereHas('plant', function ($query) use ($bu) {
                $query->where('plantname', $bu);
            })
            ->whereHas('meal.group', function ($query) use ($level) {
                $query->where('levelid', $level);
            })
            ->first();

        // dd($groupplant->mealid);

        $startDate = Carbon::parse($booking->departure_date);
        $endDate = Carbon::parse($booking->return_date);
        $startTime = Carbon::parse($booking->departure_time);
        $endTime = Carbon::parse($booking->return_time);

        // Loop by day (you can change the interval to '1 week', '1 month', etc.)
        $Alldayfood = CarbonPeriod::create($startDate, '1 day', $endDate);

        // หัวหน้างานอนุมัติ
        $oHeademp = Heademp::where('account_number', $empid)->first();
        $headempid = $oHeademp?->head_emp_id ?? "";
        $headlevel = $this->LevelEmp($headempid);
        $heademail =  $oHeademp?->head_email;
        $headname = $oHeademp?->name_head . '  ' . $oHeademp?->surname_head;
        // กลุ่มเลขา
        $approve_g = ApproveGroup::where('empid', $empid)->count();

        // dd($approve_g);

        if ($empid != "") {
            if ($typegroup == 1) {
                return view('front.expenses.index', compact(['booking', 'empid', 'empemail', 'empfullname', 'typegroup', 'plants', 'departure_date', 'return_date', 'reasons', 'totalDistance', 'groupplant', 'Alldayfood', 'startDate', 'startTime', 'endDate', 'endTime', 'empLevel', 'headempid', 'headlevel', 'heademail', 'headname', 'approve_g']));
            } else {
                echo 'ไม่ใช้ประเภทคนทั่วไป [พขร ช่าง admin ทำการเบิก]';
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fullname = Auth::user()->fullname;
        $request->validate([
            'bookid' => 'required',
            'empid' => 'required',
            'extype' => 'required',
            'returntime' => 'required',
            'totaldistance' => 'required',
            'costoffood' => 'required',
            'travelexpenses' => 'required',
            'gasolinecost' => 'required',
            'totalExpense' => 'required',
            'files.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',

        ]);

        try {
            DB::beginTransaction();

            $todayPrefix = Carbon::now()->format('Ymd');

            $latestId = Expense::where('id', 'like', $todayPrefix . '%')
                ->orderByDesc('id')
                ->first();

            $nextNumber = $latestId ? ((int)substr($latestId->id, 8)) + 1 : 1;
            $newId = $todayPrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $expense = Expense::create([
                'id' => $newId,
                'prefix' => "EX",
                'bookid' => $request->bookid,
                'empid' => $request->empid,
                'extype' => $request->extype,
                'departurefrom' => $request->departurefrom,
                'departureplant' => $request->departureplant,
                'departuretext' => null,
                'returnfrom' => $request->returnfrom,
                'returnplant' => $request->returnplant,
                'returnfromtext' => null,
                'returntime' => $request->returntime,
                'totaldistance' => $request->totaldistance,
                'latitude' => null,
                'longitude' => null,
                'checktoil' => 0,
                'fuel91id' => null,
                'fuelpricesid' => null,
                'publictransportfare' => $request->publictransportfare ?? 0,
                'expresswaytoll' => $request->expresswaytoll ?? 0,
                'otherexpenses' => $request->otherexpenses ?? 0,
                'costoffood' => $request->costoffood,
                'travelexpenses' => $request->travelexpenses,
                'gasolinecost' => $request->gasolinecost,
                'totalprice' => $request->totalExpense,
            ]);

            if (is_array($request->days)) {
                foreach ($request->days as $day) {
                    ExpenseFood::create([
                        'exid' => $expense->id,
                        'mealid' => $day['mealid'],
                        'meal1' => floatval($day['meal1'][0] ?? 0),
                        'meal2' => floatval($day['meal2'][0] ?? 0),
                        'meal3' => floatval($day['meal3'][0] ?? 0),
                        'meal4' => floatval($day['meal4'][0] ?? 0),
                        'meal1reject' => in_array("1", $day['mealx1'] ?? []) ? 1 : 0,
                        'meal2reject' => in_array("1", $day['mealx2'] ?? []) ? 1 : 0,
                        'meal3reject' => in_array("1", $day['mealx3'] ?? []) ? 1 : 0,
                        'meal4reject' => in_array("1", $day['mealx4'] ?? []) ? 1 : 0,
                        'totalprice' => floatval($day['totalprice']),
                        'totalpricebf' => floatval($day['totalpricebf']),
                        'totalreject' => intval($day['totalreject']),
                        'used_date' => $day['date'],
                    ]);
                }
            }

            // ✅ บันทึก Approve
            $token = Str::random(64);
            $approve = Approve::create([
                'exid' => $expense->id,
                'typeapprove' => 1, //ประเภทที่ 1 รอหัวหน้าอนุมัติ
                'empid' => $request->head_id,
                'email' => $request->head_email ?? '',
                'approvename' => $request->head_name ?? '',
                'emailstatus' => 1,
                'statusapprove' => 0,
                'login_token' => $token,
                'token_expires_at' => now()->addDays(10),
            ]);
            $link = route('approve.magic.login', ['token' => $approve->login_token]);
            $data = [
                'type' => 1,
                'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                'name' => $request->head_name,
                'full_name' => $fullname,
                'departuredate' => $request->departuredatemail ?? '',
                'link' => $link,
            ];


            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = Str::random() . '.' . $file->getClientOriginalExtension();
                    // $file->hashName();
                    $path = $file->storeAs('images/expenses', $filename, 'public');

                    ExpenseFile::create([
                        'exid' => $expense->id,
                        'path' => $path,
                        'etc' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();

            MailHelper::sendExternalMail(
                $request->head_email,
                'อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.exapprove', // ชื่อ blade view
                $data,
                'Expense Claim System'
            );

            return response()->json([
                'status' => 200,
                'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว',
                'expense_id' => $expense->id,
                'class' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'เกิดข้อผิดพลาดระหว่างการบันทึกข้อมูล',
                'error' => $e->getMessage(),
                'class' => 'error'
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'files' => 'required',
            'files.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf',
        ]);

        $paths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('images/expenses', 'public');

                ExpenseFile::create([
                    'exid' => $request->expense_id,
                    'path' => $path,
                ]);

                $paths[] = $path;
            }

            return response()->json([
                'status' => true,
                'message' => 'อัปโหลดสำเร็จ',
                'paths' => $paths
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'ไม่มีไฟล์ถูกอัปโหลด'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with(['vbooking', 'user'])->findOrFail($id);
        // Plant
        $plants = Plant::where('status', 1)->where('deleted', 0)
            ->get();
        $departure_date = $expense->vbooking->departure_date
            ? Carbon::parse("{$expense->vbooking->departure_date} {$expense->vbooking->departure_time}")->format('d/m/Y H:i')
            : null;

        $return_date = $expense->vbooking->return_date
            ? Carbon::parse("{$expense->vbooking->return_date} {$expense->vbooking->return_time}")->format('d/m/Y H:i')
            : null;

        $reasons = ['อบรม', 'สัมมนา', 'ฝึกงาน', 'ติดตั้งเครื่องจักร', 'ลูกค้าร้องเรียน', 'พบลูกค้า', 'อื่นๆ'];
        return view('front.expenses.view', compact(['expense', 'reasons', 'departure_date', 'return_date', 'plants']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function Heademp(Request $request)
    {
        $keyword = $request->input('sKeyword');
        $page = $request->input('page', 1);
        $limit = 5;

        $query = Valldataemp::query();

        if ($keyword) {
            $query->where('EMAIL', 'like', "%$keyword%");
        }

        $query->where('status', 1)
            ->where('deleted', 0)
            ->whereNotIn('CODEMPID', ['1234', '41000014', '23000033'])
            ->where('STAEMP', '!=', 9)
            ->where('numlvl', '>=', 7);

        $total = $query->count();

        $results = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get(['CODEMPID', 'EMAIL', 'NAMFIRSTT', 'NAMLASTT']) // ดึงหลาย field
            ->map(function ($item) {
                return [
                    'id' => $item->CODEMPID,
                    'text' => "{$item->EMAIL} | {$item->NAMFIRSTT} {$item->NAMLASTT}"
                ];
            });

        return response()->json([
            'data' => $results,
            'total_count' => $total,
        ]);
    }

    public function getAllHeadEmp(Request $request)
    {
        $empid = $request->query('emid'); // รับ emid จาก query string

        $data = Valldataemp::where('CODEMPID', $empid)
            ->where('status', 1)
            ->where('deleted', 0)
            ->whereNotIn('CODEMPID', ['1234', '41000014', '23000033', '63000455'])
            ->where('STAEMP', '!=', 9)
            ->where('numlvl', '>=', 7)
            ->first();

        // ถ้าไม่เจอข้อมูลเลย
        if (!$data) {
            return response()->json([
                'message' => 'ไม่พบข้อมูล',
            ], 404);
        }

        return response()->json([
            'Idemp' => $data->CODEMPID,
            'Emailemp' => $data->EMAIL,
            'Nameemp' => $data->NAMFIRSTT . ' ' . $data->NAMLASTT,
        ]);
    }

    private function getDistance($startName, $endName)
    {
        return DistanceRate::where(function ($q) use ($startName, $endName) {
            $q->whereHas('Startplant', function ($q2) use ($startName) {
                $q2->where('plantname', $startName);
            })->whereHas('Endplant', function ($q3) use ($endName) {
                $q3->where('plantname', $endName);
            });
        })->orWhere(function ($q) use ($startName, $endName) {
            $q->whereHas('Startplant', function ($q2) use ($endName) {
                $q2->where('plantname', $endName);
            })->whereHas('Endplant', function ($q3) use ($startName) {
                $q3->where('plantname', $startName);
            });
        })->where('deleted', 0)->value('kilometer') ?? 0;
    }

    private function LevelEmp($id)
    {
        $vAllemp = Valldataemp::where('CODEMPID', "$id")->first();
        $level = $vAllemp?->NUMLVL ?? "";
        return  $level;
    }
}
