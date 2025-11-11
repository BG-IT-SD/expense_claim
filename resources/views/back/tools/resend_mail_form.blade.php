@extends('layouts.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card p-3">
        <h5 class="card-header">
            <i class="mdi mdi-email-sync-outline"></i> เครื่องมือส่งอีเมลอนุมัติซ้ำ (Manual Resend)
        </h5>

        <div class="card-body">



            <form method="POST" action="{{ route('tools.resendMail.send') }}">
                @csrf

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">1. อีเมลผู้รับ (Head Email)</label>
                        <input type="email" name="head_email" class="form-control"
                               placeholder="ex: approver@company.com" value="{{ old('head_email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">2. ชื่อผู้รับ (Head Name)</label>
                        <input type="text" name="head_name" class="form-control"
                               placeholder="ex: คุณสมชาย อนุมัติ" value="{{ old('head_name') }}">
                    </div>

                    <hr class="my-3">

                    <div class="col-md-6">
                        <label class="form-label">3. ชื่อผู้ขอ (Full Name)</label>
                        <input type="text" name="full_name" class="form-control"
                               placeholder="ex: นายทดสอบ ระบบ" value="{{ old('full_name') }}">
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">4. ข้อความวันที่ (Departure Date String)</label>
                        <input type="text" name="departuredatemail" class="form-control"
                               placeholder="ex: 2025-11-10 - 2025-11-12" value="{{ old('departuredatemail') }}">
                    </div>


                    <div class="col-12">
                        <label class="form-label">5. ลิงก์ (Link)</label>
                        <input type="url" name="link" class="form-control"
                               placeholder="https://...." value="{{ old('link') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">6. เลขที่เอกสาร (Expense ID)</label>
                        <input type="text" name="expense_id" class="form-control"
                               placeholder="ex: ex2411-0001" value="{{ old('expense_id') }}">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-send"></i> ส่งอีเมล
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
