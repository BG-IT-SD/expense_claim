@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">
                <form action="#" method="POST" id="frmSendGroupApprove">
                    @csrf
                    <div class="card p-5">
                        <h5 class="card-header"><i class="mdi mdi-view-list"></i> สรุปรายชื่อพนักงาน
                            เบิกค่าเดินทาง/เบี้ยเลี้ยง
                        </h5>
                        <p>ประจำสัปดาห์: {{ Thaidatenow(\Carbon\Carbon::now()) }}</p>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered text-center">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>สถานที่ไปปฏิบัติงาน</th>
                                        <th>บริษัท</th>
                                        <th>รหัสพนักงาน</th>
                                        <th>ชื่อ – นามสกุล</th>
                                        <th>หน่วยงาน</th>
                                        <th>ระดับ</th>
                                        <th>เลขบัญชี</th>
                                        <th>จากวันที่</th>
                                        <th>ถึงวันที่</th>
                                        <th>จำนวนวัน</th>
                                        <th>1. ค่าเบี้ยเลี้ยง / อาหาร</th>
                                        <th>2. ค่าน้ำมัน</th>
                                        <th>3. ค่าทางด่วน</th>
                                        <th>4. ค่ารถโดยสารสาธารณะ</th>
                                        <th>5. ค่าใช้จ่ายอื่นๆ</th>
                                        <th>Total (1+2+3)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sum_food = 0;
                                        $sum_gas = 0;
                                        $sum_express = 0;
                                        $sum_publictransport = 0;
                                        $sum_other = 0;
                                        $sum_total = 0;
                                    @endphp

                                    @foreach ($expenses as $i => $expense)
                                        @php
                                            $fullname =
                                                $expense->extype == 2 || $expense->extype == 3
                                                    ? $expense->tech->fullname
                                                    : $expense->user->fullname;

                                            $days =
                                                \Carbon\Carbon::parse($expense->vbooking->departure_date)->diffInDays(
                                                    \Carbon\Carbon::parse($expense->vbooking->return_date),
                                                ) + 1;

                                            $food = $expense->costoffood ?? 0; //ค่าอาหาร
                                            $gas = $expense->gasolinecost ?? 0; // ค่าน้ำมัน
                                            $express = $expense->expresswaytoll ?? 0; //ค่าทางด่วน
                                            $publictransport = $expense->publictransportfare ?? 0; //ค่ารถสาธารณะ
                                            $other = $expense->otherexpenses ?? 0; // ค่าใช้จ่ายอื่นๆ
                                            $total = $food + $gas + $express + $publictransport + $other;

                                            $sum_food += $food; //ค่าอาหาร
                                            $sum_gas += $gas; // ค่าน้ำมัน
                                            $sum_express += $express; //ค่าทางด่วน
                                            $sum_publictransport += $publictransport; // ค่ารถสาธารณะ
                                            $sum_other += $other; // ค่าใช้จ่ายอื่นๆ
                                            $sum_total += $total;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $i + 1 }}
                                                <input type="hidden" name="expense_id[]" value="{{ $expense->id }}">

                                            </td>
                                            <td>{{ $expense->vbooking->locationbu }}</td>
                                            <td>{{ BuEmp($expense->empid) }}</td>
                                            <td>{{ $expense->empid }}</td>
                                            <td class="text-start">{{ $fullname }}</td>
                                            <td>{{ $expense->userhr->DEPT ?? '-' }}</td>
                                            <td>{{ $expense->userhr->NUMLVL ?? '-' }}</td>
                                            <td>{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($expense->vbooking->departure_date)->format('d/m/Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($expense->vbooking->return_date)->format('d/m/Y') }}
                                            </td>
                                            <td>{{ $days }}</td>
                                            <td>{{ number_format($food, 2) }}</td>
                                            <td>{{ number_format($gas, 2) }}</td>
                                            <td>{{ number_format($express, 2) }}</td>
                                            <td>{{ number_format($publictransport, 2) }}</td>
                                            <td>{{ number_format($other, 2) }}</td>
                                            <td>{{ number_format($total, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr class="table-warning fw-bold">
                                        <td colspan="11">รวม</td>
                                        <td>{{ number_format($sum_food, 2) }}
                                            <input type="hidden" name="totalfood" value="{{ $sum_food ?? 0 }}">
                                        </td>
                                        <td>{{ number_format($sum_gas, 2) }}
                                            <input type="hidden" name="totalfuel" value="{{ $sum_gas ?? 0 }}">
                                        </td>
                                        <td>{{ number_format($sum_express, 2) }}
                                            <input type="hidden" name="expresswaytoll" value="{{ $sum_express ?? 0 }}">
                                        </td>
                                        <td>{{ number_format($sum_publictransport, 2) }}
                                            <input type="hidden" name="publictransportfare"
                                                value="{{ $sum_publictransport ?? 0 }}">
                                        </td>
                                        <td>{{ number_format($sum_other, 2) }}
                                            <input type="hidden" name="otherexpenses" value="{{ $sum_other ?? 0 }}">
                                        </td>
                                        <td>
                                            {{ number_format($sum_total, 2) }}
                                            <input type="hidden" name="total" value="{{ $sum_total ?? 0 }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- NextName --}}
                        <div class="row text-center mt-5">
                            <div class="col-md-4 card shadow-none bg-transparent border border-primary mb-3">

                                <h5 class="card-header">ผู้จัดทำ</h5>
                                <div class="card-body">
                                    <h5><span class="badge rounded-pill bg-primary">{{ $makeusername ?? '-' }}</span></h5>
                                    <input type="hidden" name="created_by" value="{{ $makeuserempid ?? '' }}">
                                    <hr>
                                    HR
                                </div>

                            </div>
                            <div class="col-md-4 card shadow-none bg-transparent border border-primary mb-3">

                                <h5 class="card-header">ผู้ตรวจสอบ</h5>
                                <div class="card-body">
                                    <h5><span class="badge rounded-pill bg-primary">{{ $makeusername ?? '-' }}</span></h5>
                                    <input type="hidden" name="checkempid" value="{{ $makeuserempid ?? '' }}">
                                    <input type="hidden" name="checkname" value="{{ $makeusername ?? '' }}">
                                    <hr>
                                    HR
                                </div>

                            </div>
                            <div class="col-md-4 card shadow-none bg-transparent border border-primary mb-3">

                                <h5 class="card-header">ผู้อนุมัติ</h5>
                                <div class="card-body">
                                    <h5><span
                                            class="badge rounded-pill bg-primary">{{ $nextstaffgroup->fullname ?? '-' }}</span>
                                    </h5>
                                    <input type="hidden" name="nextmpid" value="{{ $nextstaffgroup->empid ?? '' }}">
                                    <input type="hidden" name="nextemail" value="{{ $nextstaffgroup->email ?? '' }}">
                                    <input type="hidden" name="approvename" value="{{ $nextstaffgroup->fullname ?? '' }}">
                                    <hr>
                                    HR
                                </div>

                            </div>
                            <hr>
                            <div class="col-md-12">
                                <button type="button" id="confrimapprove" class="btn btn-primary"><span
                                        class="mdi mdi-content-save"></span>
                                    ยืนยันข้อมูลและส่งอนุมัติในขั้นตอนถัดไป</button>
                            </div>
                        </div>
                        {{-- EndNextName --}}


                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
    @if (session('message'))
        <script>
            Swal.fire({
                title: {!! json_encode(session('message')) !!}, // ✅ ป้องกัน Error ใน JavaScript
                icon: {!! json_encode(session('class')) !!},
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        </script>
    @endif
    <script>
        const hrHeadApproveUrl = "{{ route('HR.hrheadapprove') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/approve.js']) }}"></script>
@endsection
