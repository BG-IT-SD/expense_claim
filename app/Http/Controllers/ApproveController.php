<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Models\Approve;
use App\Models\ApproveStaff;
use App\Models\Exgroup;
use App\Models\Expense;
use App\Models\GroupSpecial;
use App\Models\User;
use App\Models\Valldataemp;
use App\Models\Vbooking;
use App\Models\Vbookingall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApproveController extends Controller
{
    public function show($id)
    {
        $nextempid = '';
        $nextemail = '';
        $nextfullname = '';
        $nextStepApprove = '';
        $nextempid = '';
        $nextemail = '';
        $nextfullname = '';

        $approve = Approve::with('expense')->findOrFail($id);
        // if (!Auth::check() && session('approve_access') != $id) {
        //     abort(403, 'Unauthorized');
        // }


        $expense = $approve->expense;
        $user = User::where('empid', $expense->empid)->first(); // กรณีมี user
        $booking = Vbookingall::where('id', $expense->bookid)->first();
        // dd($expense);
        // $departure_date = $booking->departure_date ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i') : null;
        // $return_date = $booking->return_date ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i') : null;
        $departure_date = null;
        $return_date    = null;

        if ($booking) {
            $departure_date = $booking->departure_date
                ? Carbon::parse("{$booking->departure_date} {$booking->departure_time}")->format('d/m/Y H:i')
                : null;

            $return_date = $booking->return_date
                ? Carbon::parse("{$booking->return_date} {$booking->return_time}")->format('d/m/Y H:i')
                : null;
        }

        $bu = BuEmp($expense->empid);
        // เช็คกลุ่ม

        $groupapprove = GroupSpecial::where('empid', $expense->empid)->where('deleted', 0)->first();
        $groupData = $groupapprove->groupapprove ?? 1;
        $extype = $expense->extype ?? 1;

        if ($extype == 2 || $extype == 3) {
            // new extype 2
            if ($approve->typeapprove == 2) {

                $nextStepApprove = Approvestep($bu, $extype, 2, $groupData);
                $nextempid = $nextStepApprove['empid'];
                $nextemail = $nextStepApprove['email'];
                $nextfullname = $nextStepApprove['fullname'];
            }
        } else {
            // dd($extype);
            if ($approve->typeapprove == 4) {
                if ($extype == 2) {
                    $nextStepApprove = Approvestep($bu, 1, 2);
                } else {
                    $nextStepApprove = Approvestep($bu, $extype, 2);
                }

                $nextempid = $nextStepApprove['empid'];
                $nextemail = $nextStepApprove['email'];
                $nextfullname = $nextStepApprove['fullname'];
            }
        }



        $exMail = '';
        $exName = '';
        if ($extype == 2 || $extype == 3) {
            $vAllemp = Valldataemp::where('CODEMPID', $expense->empid)->where('STAEMP', '!=', '9')->first();
            $exMail = $vAllemp->EMAIL;
            $exName = $vAllemp->NAMFIRSTT . ' ' . $vAllemp->NAMLASTT;
        }


        return view('approve.approve', compact('approve', 'expense', 'user', 'booking', 'departure_date', 'return_date', 'nextempid', 'nextemail', 'nextfullname', 'exName', 'exMail'));
    }


    public function confirm(Request $request, $id)
    {
        $approve = Approve::findOrFail($id);
        $action = $request->input('action');
        $reason = $request->input('reason');
        $typeapprove = $request->input('typeapprove');
        $expenseempid = $request->input('expenseempid');
        $departuredate = $request->input('departuredate');
        $approvename = $request->input('approvename');
        $expenseid = $request->input('expenseid');
        $empemail = $request->input('empemail');
        $empfullname = $request->input('empfullname');

        if ($approve->statusapprove !== 0) {
            return back()->with([
                'message' => 'คุณได้ดำเนินการไปแล้ว',
                'class' => 'error'
            ]);
        }

        if (now()->greaterThan($approve->token_expires_at)) {
            return back()->with([
                'message' => 'ลิงก์หมดอายุแล้ว',
                'class' => 'error'
            ]);
        }

        if ($action === 'reject' && empty($reason)) {
            return back()->with([
                'message' => 'กรุณากรอกเหตุผลที่ไม่อนุมัติ',
                'class' => 'error'
            ]);
        }

        $approve->statusapprove = $request->action === 'approve' ? 1 : 2;
        $approve->remark = $reason; // หรือ reject_reason
        $approve->save();

        // ✅ ส่งเมลเฉพาะกรณี reject
        if ($request->action === 'reject') {
            $data = [
                'headname' => $approvename, // คนที่ reject
                'name' => $empfullname, // user
                'expenseid' => $approve->exid, //exid
                'departuredate' => $departuredate,
                'remark' => $reason,
            ];

            MailHelper::sendExternalMail(
                $empemail, // ผู้รับ คือ ผู้ขอเบิก
                'แจ้งผลการไม่อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.reject', // ชื่อ blade view mail
                $data,
                'Expense Claim System EX' . $approve->exid,
            );
        }

        $nextempid = '';
        $nextemail = '';
        $nextfullname = '';

        if ($approve->typeapprove == 4 || $approve->typeapprove == 2) {
            // ✅ หากอนุมัติสำเร็จ สร้าง approve ถัดไป
            if ($approve->statusapprove === 1) {

                $nextempid = $request->input('nextempid');
                $nextemail = $request->input('nextemail');
                $nextfullname = $request->input('nextfullname');
                // $nextempid = '66000510';
                // $nextemail = 'kamolwan.b@bgiglass.com';
                // $nextfullname = 'กมลวรรณ บรรชา';
                // ตั้งค่าข้อมูลผู้อนุมัติถัดไป (HR ผุู้จัดการฝ่าย)
                if ($approve->typeapprove == 2) {
                    $nextType = 1;
                } else {
                    $nextType = $approve->typeapprove + 1;
                }


                $token = Str::random(64);
                $nextApprove = Approve::create([
                    'exid' => $approve->exid,
                    'typeapprove' => $nextType,
                    'empid' => $nextempid,
                    'email' => $nextemail,
                    'approvename' => $nextfullname,
                    'emailstatus' => 1,
                    'statusapprove' => 0,
                    'login_token' => $token,
                    'token_expires_at' => now()->addDays(10),
                ]);

                // ✅ ส่งอีเมลลิงก์อนุมัติรอบถัดไป
                $link = route('approve.magic.login', ['token' => $token]);

                $data = [
                    'type' => $nextType,
                    'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                    'name' => $nextfullname,
                    'full_name' => $empfullname,
                    'departuredate' => $departuredate ?? '',
                    'check_hr' => $approvename,
                    'link' => $link,
                ];

                MailHelper::sendExternalMail(
                    $nextemail,
                    'อนุมัติการเบิกเบี้ยเลี้ยง',
                    'mails.hrheadapprove',
                    $data,
                    'Expense Claim System EX' . $approve->exid,
                );
            }
        }

        // บัญชีอนุมัติเรียบร้อย
        if ($approve->typeapprove == 6 && $approve->statusapprove === 1) {
            // อนุมัติขั้นตอนสุดท้ายเสร็จ
            // $linksuccess = route('approve.magic.login', ['token' => $token]);

            $data = [
                'type' => 5,
                'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                'name' => $empfullname,
                'full_name' => $empfullname,
                'departuredate' => $departuredate ?? '',
            ];

            MailHelper::sendExternalMail(
                $empemail,
                'อนุมัติการเบิกเบี้ยเลี้ยง',
                'mails.success',
                $data,
                'Expense Claim System EX' . $approve->exid,
            );
        }
        // บัญชีอนุมัติเรียบร้อย

        return back()->with([
            'message' => 'บันทึกผลอนุมัติเรียบร้อย',
            'class' => 'success'
        ]);
    }

    public function showgroup($id, $type)
    {
        //ดึงรายการ approve ทั้งหมดในกลุ่ม
        $approve = Approve::where('exgroup', $id)
            ->where('typeapprove', $type)
            ->orderBy('typeapprove', 'desc')
            ->first();

        //ดึงรายการเบิกทั้งหมดในกลุ่ม พร้อม relations
        $expenses = Expense::with(['vbooking', 'user', 'tech', 'userhr'])
            ->where('exgroup', $id)
            ->get();
        $makeuserempid = $approve->empid;
        //ดึงข้อมูลกลุ่ม
        $exgroup = Exgroup::findOrFail($id);

        $nextstaffgroup = ApproveStaff::where('group', function ($query) use ($makeuserempid) {
            $query->select('group')
                ->from('approvestaff')
                ->where('empid', $makeuserempid)
                ->where('status', 1)
                ->where('deleted', 0)
                ->limit(1);
        })
            ->where('step', 2)
            ->where('extype', 1)
            ->where('status', 1)
            ->where('deleted', 0)
            ->first();
        // dd($nextstaffgroup);
        if ($type == 5) {
            $nextempid = '99999999';
            $nextfullname = 'หน่วยงานบัญชี';
            $nextemail = 'account_auto@bgiglass.com';
        } else {
            $nextempid = $nextstaffgroup->empid ?? '';
            $nextfullname = $nextstaffgroup->fullname ?? '';
            $nextemail = $nextstaffgroup->email ?? '';
            // $nextemail = 'Kamolwan.b@bgiglass.com';
        }

         $page = 'HR.show';

        return view('approve.approve_group', compact('approve', 'expenses', 'exgroup', 'nextstaffgroup', 'nextempid', 'nextfullname', 'nextemail','type','page'));
    }

    public function confirmgroup(Request $request, $id)
    {

        $approve = Exgroup::findOrFail($id);
        $approveex = Approve::where('exgroup', $id)->first();

        $action = $request->input('action');
        $reason = $request->input('reason');
        $typeapprove = $request->input('typeapprove');
        $approvename = $request->input('approvename');
        $nextempid = $request->input('nextempid');
        $nextfullname = $request->input('nextfullname');
        $nextemail = $request->input('nextemail');
        $expenseidgroup = $request->input('expenseidgroup');
        $groupdate = $request->filled('groupdate')
            ? Carbon::parse($request->input('groupdate'))->format('d/m/Y')
            : null;
        $expensegroupdata = Approve::where('exgroup', $id)
            ->where('deleted', 0)
            ->where('status', 1)
            ->where('typeapprove', $typeapprove)
            ->get();



        if ($approve->statusapprove !== 0) {
            return back()->with([
                'message' => 'คุณได้ดำเนินการไปแล้ว',
                'class' => 'error'
            ]);
        }


        if (now()->greaterThan($approveex->token_expires_at)) {
            return back()->with([
                'message' => 'ลิงก์หมดอายุแล้ว',
                'class' => 'error'
            ]);
        }

        if ($action === 'reject' && empty($reason)) {
            return back()->with([
                'message' => 'กรุณากรอกเหตุผลที่ไม่อนุมัติ',
                'class' => 'error'
            ]);
        }

        try {
            DB::beginTransaction();

            // 2. อัปเดต Approve ทั้งหมดในกลุ่มนี้
            Approve::where('exgroup', $id)->where('typeapprove', $typeapprove)->update([
                'statusapprove' => ($action === 'approve') ? 1 : 2,
                'remark' => $reason ?? null,
            ]);
            $nexttypeapprove = $typeapprove + 1;
            // 3. ถ้า approve สร้าง approve ใหม่ (ขั้นถัดไป)
            $token = Str::random(64);
            if ($action === 'approve') {
                foreach ($expensegroupdata as $exid) {
                    Approve::create([
                        'exid' => $exid->exid,
                        'typeapprove' => $nexttypeapprove,
                        'empid' => $nextempid,
                        'email' => $nextemail,
                        'approvename' => $nextfullname,
                        'emailstatus' => 1,
                        'statusapprove' => 0,
                        'login_token' => $token,
                        'token_expires_at' => now()->addDays(10),
                        'exgroup' => $id,
                    ]);
                }

                //อัปเดต exgroup
                $approve->typeapprove = $nexttypeapprove;
                $approve->statusapprove = 0;
                if ($typeapprove == 4) {
                    $approve->finalempid = $nextempid;
                    $approve->finalemail = $nextemail;
                }
                $approve->save();
            } else {
                // ถ้าไม่อนุมัติ อัปเดต exgroup
                $approve->statusapprove = 2;
                $approve->save();
            }



            DB::commit();

            if (in_array($typeapprove, [4, 5])) {
                $link = route('approve.magic.login', ['token' => $token]);
                MailHelper::sendExternalMail(
                    $nextemail,
                    'แจ้งเตือนการอนุมัติรายการกลุ่ม',
                    'mails.groupapprove',
                    [
                        'name' => $nextfullname,
                        'groupid' => $id,
                        'count' => $expensegroupdata->count(),
                        'groupdate' => $groupdate,
                        'checkname' => $approvename,
                        'link' => $link
                    ],
                    'รายการขออนุมัติกลุ่ม EXGROUP-' . $id . ' วันที่ ' . $groupdate
                );
            }


            return redirect()->route('approve.page.group', ['id' => $id, 'type' => $typeapprove])
                ->with(['message' => 'บันทึกผลอนุมัติแล้ว', 'class' => 'success']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }


    public function hrrject(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|integer|exists:expenses,id',
            'reason'     => 'required|string',
        ], [
            'expense_id.required' => 'ไม่พบรหัสรายการเบิก',
            'reason.required'     => 'กรุณากรอกเหตุผล',
        ]);

        $expenseId = (int) $request->expense_id;
        $reason    = $request->reason;
        $empid     = Auth::user()->empid ?? null; // คนที่กด reject
        $typeapprove = $request->typeapprove;


        try {
            DB::transaction(function () use ($expenseId, $reason, $empid,$typeapprove) {

                // 1หา approve แถวล่าสุดของ exid นี้ (เช่น typeapprove 4 = HRตรวจสอบแล้ว)
                $approve = Approve::where('exid', $expenseId)
                    ->where('typeapprove', $typeapprove)
                    ->where('deleted', 0)
                    ->whereNotNull('exgroup')
                    ->orderByDesc('id')
                    ->first();

                if ($approve) {
                    $approve->update([
                        'statusapprove' => 2,          // 2 = Reject
                        'remark'        => $reason,
                        'empid'         => $empid ?? $approve->empid,
                        'exgroup'        => null,
                        'login_token'    => null,
                    ]);
                }

                // 2) ปรับยอดรวมใน exgroup
               $expense = Expense::with('exgroupData')->findOrFail($expenseId);
                $exgroup = $expense->exgroupData;

                if (!$exgroup) {
                    throw new \Exception("รายการนี้ไม่อยู่ในกลุ่ม จึงไม่สามารถหักยอดรวมได้");
                }


                if ($exgroup) {
                    // ====== ยอดของบรรทัดนี้ (จาก expenses table) ======
                    $food   = $expense->costoffood          ?? 0;
                    $fuel   = $expense->gasolinecost        ?? 0;
                    $exp    = $expense->expresswaytoll      ?? 0;
                    $public = $expense->publictransportfare ?? 0;
                    $other  = $expense->otherexpenses       ?? 0;

                    // ถ้าคุณใช้ totalprice เป็นยอดรวมต่อบรรทัดก็มีให้ใช้
                    $lineTotal = $expense->totalprice ?? 0;

                    // ====== หักออกจากยอดรวมใน exgroup (ตามโครงในรูป) ======

                    // 1) ยอดหลักแต่ละประเภท
                    $exgroup->totalfood           = max(0, ($exgroup->totalfood           ?? 0) - $food);
                    $exgroup->totalfuel           = max(0, ($exgroup->totalfuel           ?? 0) - $fuel);
                    $exgroup->expresswaytoll      = max(0, ($exgroup->expresswaytoll      ?? 0) - $exp);
                    $exgroup->publictransportfare = max(0, ($exgroup->publictransportfare ?? 0) - $public);
                    $exgroup->otherexpenses       = max(0, ($exgroup->otherexpenses       ?? 0) - $other);

                    // 2) totalother = expressway + public + other
                    $exgroup->totalother = max(
                        0,
                        ($exgroup->expresswaytoll      ?? 0)
                            + ($exgroup->publictransportfare ?? 0)
                            + ($exgroup->otherexpenses       ?? 0)
                    );

                    // 3) total = food + fuel + totalother
                    $exgroup->total = max(
                        0,
                        ($exgroup->totalfood  ?? 0)
                            + ($exgroup->totalfuel  ?? 0)
                            + ($exgroup->totalother ?? 0)
                    );

                    // ✅ ถ้าต้องการให้ column net* เดินตาม total ไปด้วยแบบง่าย ๆ
                    // (ถ้าคุณมี logic ภาษี/หักอื่น ๆ ให้เปลี่ยนตรงนี้ทีหลังได้)
                    $exgroup->netexpresswaytoll      = $exgroup->expresswaytoll;
                    $exgroup->netpublictransportfare = $exgroup->publictransportfare;
                    $exgroup->netotherexpenses       = $exgroup->otherexpenses;
                    $exgroup->nettotalfood           = $exgroup->totalfood;
                    $exgroup->nettotalfuel           = $exgroup->totalfuel;

                    $exgroup->nettotalother = max(
                        0,
                        ($exgroup->netexpresswaytoll      ?? 0)
                            + ($exgroup->netpublictransportfare ?? 0)
                            + ($exgroup->netotherexpenses       ?? 0)
                    );

                    $exgroup->nettotal = max(
                        0,
                        ($exgroup->nettotalfood  ?? 0)
                            + ($exgroup->nettotalfuel  ?? 0)
                            + ($exgroup->nettotalother ?? 0)
                    );

                    $exgroup->save();

                    // 4) ตัด expense ออกจากกลุ่มด้วย (กันไม่ให้ยอดรวมผิดรอบหน้า)
                    $expense->exgroup = null;

                    // ถ้ามี status สำหรับสถานะ reject ก็อาจตั้งตรงนี้ เช่น 2 หรือ 9 ตามระบบคุณ
                    // $expense->status = 9;

                    $expense->save();
                }
            });

            return response()->json([
                'status'  => 200,
                'message' => 'บันทึกการ Reject และอัปเดตรวมกลุ่มเรียบร้อยแล้ว',
                'class'   => 'success',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'เกิดข้อผิดพลาดระหว่างบันทึกข้อมูล',
                'error'   => $e->getMessage(),
                'class'   => 'error',
            ], 500);
        }
    }
}
