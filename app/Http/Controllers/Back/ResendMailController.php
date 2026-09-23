<?php

namespace App\Http\Controllers\Back;

use App\Helpers\MailHelper;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\Exgroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

class ResendMailController extends Controller
{
    public function showForm()
    {
        $approvals = Approve::query()
            ->with(['expense.user', 'expense.tech', 'expense.vbooking', 'expense.vbookingdrv'])
            ->where('statusapprove', 0)
            ->where('status', 1)
            ->where('deleted', 0)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereHas('expense')
            ->latest('id')
            ->get()
            ->unique(fn (Approve $approve) => $approve->exgroup
                ? 'group-'.$approve->exgroup
                : 'approval-'.$approve->id)
            ->map(fn (Approve $approve) => [
                'id' => $approve->id,
                'document' => $approve->exgroup
                    ? 'EXGROUP-'.$approve->exgroup
                    : 'EX'.$approve->exid,
                'requester' => $this->requesterName($approve),
                'approver' => $approve->approvename ?: '-',
                'email' => $approve->email,
                'preview_url' => route('tools.resendMail.preview', $approve->id),
            ])
            ->values();

        return view('back.tools.resend_mail_form', compact('approvals'));
    }

    public function preview(int $approve)
    {
        $approval = Approve::query()
            ->with(['expense.user', 'expense.tech', 'expense.vbooking', 'expense.vbookingdrv'])
            ->whereKey($approve)
            ->where('statusapprove', 0)
            ->where('status', 1)
            ->where('deleted', 0)
            ->firstOrFail();

        abort_unless($approval->expense, 404);

        $token = $approval->login_token ?: 'preview';
        $mail = $approval->exgroup
            ? $this->groupMailPayload($approval, $token)
            : $this->expenseMailPayload($approval, $token);

        return response()->view($mail['view'], $mail['data']);
    }

    public function sendMail(Request $request)
    {
        $validated = $request->validate([
            'approve_id' => ['required', 'integer', 'exists:approve,id'],
        ], [
            'approve_id.required' => 'กรุณาเลือกรายการที่ต้องการส่งอีเมลซ้ำ',
        ]);

        $approve = Approve::query()
            ->with(['expense.user', 'expense.tech', 'expense.vbooking', 'expense.vbookingdrv'])
            ->whereKey($validated['approve_id'])
            ->where('statusapprove', 0)
            ->where('status', 1)
            ->where('deleted', 0)
            ->first();

        if (!$approve || !$approve->expense) {
            return back()->withInput()->with('error', 'รายการนี้ไม่ได้อยู่ระหว่างรออนุมัติแล้ว');
        }

        if (!filter_var($approve->email, FILTER_VALIDATE_EMAIL)) {
            return back()->withInput()->with('error', 'อีเมลผู้อนุมัติในระบบไม่ถูกต้อง');
        }

        try {
            $token = $approve->login_token ?: Str::random(64);

            if ($approve->exgroup) {
                Approve::query()
                    ->where('exgroup', $approve->exgroup)
                    ->where('statusapprove', 0)
                    ->where('status', 1)
                    ->where('deleted', 0)
                    ->update([
                        'login_token' => $token,
                        'token_expires_at' => now()->addDays(10),
                    ]);

                $this->sendGroupMail($approve, $token);
                $document = 'EXGROUP-'.$approve->exgroup;
            } else {
                $approve->update([
                    'login_token' => $token,
                    'token_expires_at' => now()->addDays(30),
                ]);

                $this->sendExpenseMail($approve, $token);
                $document = 'EX'.$approve->exid;
            }

            logAction(
                'resend-mail',
                'Approve',
                'ส่งอีเมลอนุมัติซ้ำ '.$document.' ไปยัง '.$approve->email,
                json_encode(['approve_id' => $approve->id, 'document' => $document])
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'ส่งอีเมลไม่สำเร็จ: '.$e->getMessage());
        }

        return back()->with('success', 'ส่งอีเมลอนุมัติซ้ำ '.$document.' ไปยัง '.$approve->email.' สำเร็จแล้ว');
    }

    private function sendExpenseMail(Approve $approve, string $token): void
    {
        $mail = $this->expenseMailPayload($approve, $token);
        $response = MailHelper::sendExternalMail(
            $approve->email,
            $mail['subject'],
            $mail['view'],
            $mail['data'],
            $mail['from']
        );

        if ($response === false) {
            throw new RuntimeException('บริการส่งอีเมลไม่ตอบกลับ');
        }
    }

    private function sendGroupMail(Approve $approve, string $token): void
    {
        $mail = $this->groupMailPayload($approve, $token);
        $response = MailHelper::sendExternalMail(
            $approve->email,
            $mail['subject'],
            $mail['view'],
            $mail['data'],
            $mail['from']
        );

        if ($response === false) {
            throw new RuntimeException('บริการส่งอีเมลไม่ตอบกลับ');
        }
    }

    private function expenseMailPayload(Approve $approve, string $token): array
    {
        return [
            'subject' => 'อนุมัติการเบิกเบี้ยเลี้ยง',
            'view' => 'mails.exapprove',
            'data' => [
                'type' => 1,
                'title' => 'แจ้งเตือนการอนุมัติการเบิกเบี้ยเลี้ยง',
                'name' => $approve->approvename ?: '-',
                'full_name' => $this->requesterName($approve),
                'departuredate' => $this->departureDate($approve),
                'link' => route('approve.magic.login', ['token' => $token]),
            ],
            'from' => 'Expense Claim System EX'.$approve->exid,
        ];
    }

    private function groupMailPayload(Approve $approve, string $token): array
    {
        $group = Exgroup::with('user')->findOrFail($approve->exgroup);
        $count = Approve::query()
            ->where('exgroup', $group->id)
            ->where('statusapprove', 0)
            ->where('status', 1)
            ->where('deleted', 0)
            ->count();
        $groupDate = $group->groupdate
            ? Carbon::parse($group->groupdate)->format('d/m/Y')
            : now()->format('d/m/Y');

        return [
            'subject' => 'แจ้งเตือนอนุมัติกลุ่มรายการเบิกเบี้ยเลี้ยงวันที่ '.$groupDate,
            'view' => 'mails.groupapprove',
            'data' => [
                'name' => $approve->approvename ?: '-',
                'groupid' => $group->id,
                'count' => $count,
                'groupdate' => $groupDate,
                'checkname' => optional($group->user)->fullname ?: $group->checkempid,
                'link' => route('approve.magic.login', ['token' => $token]),
            ],
            'from' => 'รายการขออนุมัติกลุ่ม EXGROUP-'.$group->id.' วันที่ '.$groupDate,
        ];
    }

    private function requesterName(Approve $approve): string
    {
        $expense = $approve->expense;

        if (!$expense) {
            return '-';
        }

        return in_array((int) $expense->extype, [2, 3], true)
            ? optional($expense->tech)->fullname ?? '-'
            : optional($expense->user)->fullname ?? '-';
    }

    private function departureDate(Approve $approve): string
    {
        $expense = $approve->expense;
        $booking = (int) optional($expense)->extype === 2
            ? optional($expense)->vbookingdrv
            : optional($expense)->vbooking;

        if (!$booking || !$booking->departure_date) {
            return '-';
        }

        $departure = Carbon::parse($booking->departure_date)->format('d/m/Y');
        $return = $booking->return_date
            ? Carbon::parse($booking->return_date)->format('d/m/Y')
            : null;

        return $return && $return !== $departure
            ? $departure.' - '.$return
            : $departure;
    }
}
