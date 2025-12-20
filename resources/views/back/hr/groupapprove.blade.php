@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">
                <form action="#" method="POST" id="frmSendGroupApprove">
                    @csrf
                    <div class="card p-5">
                        <h5 class="card-header"><i class="mdi mdi-view-list"></i> สรุปรายชื่อพนักงาน
                            เบิกค่าเดินทาง/เบี้ยเลี้ยง {{ $plantName }}
                        </h5>
                        <p>ประจำสัปดาห์: {{ Thaidatenow(\Carbon\Carbon::now()) }}</p>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered text-center">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>EXID</th>
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

                                            // เลือก booking
                                            $booking =
                                                $expense->extype == 2 ? $expense->vbookingdrv : $expense->vbooking;

                                            $startDate = optional($booking)->departure_date;
                                            $endDate = null;

                                            if ($expense->extype == 2) {
                                                // ถ้ามี bookid ใน logs แนะนำกรองให้ตรง booking ด้วย
                                                $logs = $expense->logs;
                                                // $logs = $logs->where('bookid', optional($booking)->id); // <-- uncomment ถ้า logs มี bookid

                                                $lastLog = $logs->sortByDesc('id')->first();

                                                if (
                                                    $lastLog &&
                                                    preg_match('/\d{4}-\d{2}-\d{2}/', $lastLog->remark, $m)
                                                ) {
                                                    $endDate = $m[0];
                                                } else {
                                                    $endDate = optional($booking)->return_date; // fallback
                                                }
                                            } else {
                                                $endDate = optional($booking)->return_date;
                                            }

                                            $days =
                                                $startDate && $endDate
                                                    ? \Carbon\Carbon::parse($startDate)->diffInDays(
                                                            \Carbon\Carbon::parse($endDate),
                                                            true,
                                                        ) + 1
                                                    : 0;

                                            $food = $expense->costoffood ?? 0;
                                            $gas = $expense->gasolinecost ?? 0;
                                            $express = $expense->expresswaytoll ?? 0;
                                            $publictransport = $expense->publictransportfare ?? 0;
                                            $other = $expense->otherexpenses ?? 0;

                                            $total = $food + $gas + $express + $publictransport + $other;

                                            $sum_food += $food;
                                            $sum_gas += $gas;
                                            $sum_express += $express;
                                            $sum_publictransport += $publictransport;
                                            $sum_other += $other;
                                            $sum_total += $total;
                                            $sumtotalother = $sum_express + $sum_publictransport + $sum_other;
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $i + 1 }}
                                                <input type="hidden" name="expense_id[]" value="{{ $expense->id }}">

                                            </td>
                                            <td> {{ 'EX' . $expense->id }}</td>
                                            @if ($expense->extype == 2)
                                                <td>{{ $expense->vbookingdrv->display_location }}</td>
                                            @else
                                                <td>{{ $expense->vbooking->display_location }}</td>
                                            @endif
                                            <td>{{ BuEmp($expense->empid) }}</td>
                                            <td>{{ $expense->empid }}</td>
                                            <td class="text-start">{{ $fullname }}</td>
                                            <td>{{ $expense->userhr->DEPT ?? '-' }}</td>
                                            <td>{{ $expense->userhr->JOBGRADE_TITLE ?? '-' }}</td>
                                            <td>{{ $expense->userhr->NUMBANK ?? '-' }}</td>
                                            <td>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}
                                            </td>

                                            <td>{{ $days ?: '-' }}</td>
                                            <td>{{ number_format($food, 2) }}</td>
                                            <td>{{ number_format($gas, 2) }}</td>
                                            <td>{{ number_format($express, 2) }}</td>
                                            <td>{{ number_format($publictransport, 2) }}</td>
                                            <td>{{ number_format($other, 2) }}</td>
                                            <td>{{ number_format($total, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr class="table-warning fw-bold">
                                        <td colspan="12">รวม</td>
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
                                            {{ number_format(round($sum_total), 2) }}
                                            <input type="hidden" name="total" value="{{ round($sum_total) ?? 0 }}">
                                            <input type="hidden" name="totalother" value="{{ $sumtotalother ?? 0 }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 mt-3 mb-3">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr class="table-info">
                                        <th>
                                            <h6>รายละเอียด</h6>
                                        </th>
                                        <th>
                                            <h6>จำนวนเงินขอเบิก / บาท</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ค่าอาหาร</td>
                                        <td>
                                            <span class="btn rounded-pill btn-primary waves-effect waves-light">
                                                {{ number_format($sum_food, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>ค่าเดินทาง และ อื่นๆ</td>
                                        <td>
                                            <span
                                                class="btn rounded-pill btn-primary waves-effect waves-light totaltravel">{{ number_format($sumtotalother, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>ค่าน้ำมัน</td>
                                        <td>
                                            <span
                                                class="btn rounded-pill btn-primary waves-effect waves-light gasolinecost">{{ number_format($sum_gas, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>รวม</td>
                                        <td>
                                            <span
                                                class="btn rounded-pill btn-success waves-effect waves-light totalExpense">
                                                {{ number_format(round($sum_total), 2) }}</span>
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
                            <input type="hidden" name="plantid" value="{{ $plantID }}">
                            <input type="hidden" name="plantname" value="{{ $plantName }}">
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
