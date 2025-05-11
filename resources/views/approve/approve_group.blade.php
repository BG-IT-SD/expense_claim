@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Search --}}
        @if (now()->lessThan($approve->token_expires_at))
            <div class="row">
                <!-- Basic Layout -->
                <div class="col-xxl-12">
                    @if (session('message'))
                        {{-- alert --}}
                        <div class="alert alert-{{ session('class') }} alert-dismissible h4" role="alert">
                            <span class="mdi mdi-bell"></span> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        {{--  alert --}}
                    @endif
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h3 class="mb-0"><span class="mdi mdi-file-document-check h3"></span> รายการขออนุมัติกลุ่ม:
                                {{ ' EXGROUP-' . $exgroup->id . ' วันที่ ' . $exgroup->groupdate }}</h3>
                        </div>
                        <div class="card-body row">
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

                                                $days =
                                                    \Carbon\Carbon::parse(
                                                        $expense->vbooking->departure_date,
                                                    )->diffInDays(
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
                                                $sumtotalother = $sum_express + $sum_publictransport + $sum_other;
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $i + 1 }}
                                                    <input type="hidden" name="expense_id[]" value="{{ $expense->id }}">

                                                </td>
                                                <td> {{ 'EX' . $expense->id }}</td>
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
                                            <td colspan="12">รวม</td>
                                            <td>{{ number_format($sum_food, 2) }}
                                            </td>
                                            <td>{{ number_format($sum_gas, 2) }}
                                            </td>
                                            <td>{{ number_format($sum_express, 2) }}
                                            </td>
                                            <td>{{ number_format($sum_publictransport, 2) }}
                                            </td>
                                            <td>{{ number_format($sum_other, 2) }}
                                            </td>
                                            <td>
                                                {{ number_format($sum_total, 2) }}
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
                                                    {{ number_format($exgroup->totalfood, 2) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ค่าเดินทาง และ อื่นๆ</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light totaltravel">{{ number_format($exgroup->totalother, 2) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ค่าน้ำมัน</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light gasolinecost">{{ number_format($exgroup->totalfuel, 2) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>รวม</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-success waves-effect waves-light totalExpense">
                                                    {{ number_format($exgroup->total, 2) }}</span>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="card mb-4">
                        <div class="card-body row">
                            @if ($approve->typeapprove == 4)
                                <div class="col-sm-6 mb-3">
                                    <h4><strong>ผู้ตรวจสอบ:</strong> {{ $exgroup->checkempid ?? '-' }}</h4>

                                </div>
                                <div class="col-sm-6 mb-3">
                                    <h4> {{ $exgroup->user->fullname ?? '' }}</h4>

                                </div>
                            @endif

                            <div class="col-sm-6 mb-3">
                                <h4><strong>สถานะ:</strong> {!! status_approve_badge($approve->statusapprove, $approve->typeapprove) !!}</h4>

                            </div>
                            <div class="col-sm-6 mb-3">
                                <h4><strong>ประเภท:</strong> {!! type_approve_text($approve->typeapprove) !!}</h4>

                            </div>
                            <div class="col-sm-6 mb-3">
                                <h4><strong>ผู้อนุมัติ:</strong> {{ $approve->empid . ' | ' . $approve->approvename }}</h4>

                            </div>
                            @if ($approve->typeapprove == 4)
                                <div class="col-sm-6 mb-3">
                                    <h4><strong>ผู้อนุมัติขั้นถัดไป:</strong> {{ $nextempid . ' | ' . $nextfullname }}</h4>
                                </div>
                            @endif
                            @if ($approve->statusapprove == 0)
                                <hr>
                                <div class="col-sm-12 mb-3 text-center">

                                    <form id="approveForm" method="POST"
                                        action="{{ route('approve.confirmgroup', $exgroup->id) }}">
                                        @csrf
                                        <div id="reject-reason-box" class="mb-3" style="display: none;">
                                            <label for="reason" class="form-label">
                                                <h3>เหตุผลที่ไม่อนุมัติ</h3>
                                            </label>
                                            <textarea name="reason" id="reason" class="form-control" rows="3"></textarea>

                                        </div>
                                        <input type="hidden" id="typeapprove" name="typeapprove"
                                            value="{{ $approve->typeapprove }}">
                                        <input type="hidden" name="approveempid" id="approveempid"
                                            value="{{ $approve->empid }}">
                                        <input type="hidden" name="approvename" id="approvename"
                                            value="{{ $approve->approvename }}">

                                        <input type="hidden" name="nextempid" id="nextempid" value="{{ $nextempid }}">
                                        <input type="hidden" name="nextfullname" id="nextfullname"
                                            value="{{ $nextfullname }}">
                                        <input type="hidden" name="nextemail" id="nextemail" value="{{ $nextemail }}">

                                        <input type="hidden" name="expenseidgroup" id="expenseidgroup"
                                            value="{{ 'EXGROUP-' . $exgroup->id }}">
                                        <input type="hidden" name="groupdate" id="groupdate"
                                            value="{{ $exgroup->groupdate }}">


                                        <button type="button" name="action" value="approve" class="btn btn-success"
                                            id="btnApprove">
                                            <span class="mdi mdi-check-circle"></span> อนุมัติ</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger"
                                            id="btnReject">
                                            <span class="mdi mdi-close-circle-outline"></span> ไม่อนุมัติ</button>

                                        <input type="hidden" name="action" id="actionInput" value="">
                                    </form>
                                </div>
                            @endif


                        </div>

                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning mt-3">
                ไม่สามารถดำเนินการได้ (อาจหมดอายุหรือตอบกลับไปแล้ว)
            </div>
        @endif
    </div>
@endsection
@section('jscustom')
    <script>
        $(document).ready(function() {
            let isSubmitting = false;

            // เมื่อกดปุ่ม "อนุมัติ"
            $('#btnApprove').click(function(e) {
                if (isSubmitting) return; // ป้องกันกดรัว

                Swal.fire({
                    icon: 'question',
                    title: 'ยืนยันการอนุมัติ',
                    text: 'คุณต้องการอนุมัติรายการนี้ใช่หรือไม่?',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, อนุมัติ',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {
                    if (result.isConfirmed) {
                        isSubmitting = true;

                        $('#actionInput').val('approve');
                        $('#btnApprove, #btnReject').prop('disabled', true); // ปิดทั้ง 2 ปุ่ม
                        $('#approveForm').submit();
                    }
                });
            });

            // เมื่อกดปุ่ม "ไม่อนุมัติ"
            $('#btnReject').click(function(e) {
                if (isSubmitting) return; // ป้องกันกดรัว

                const reason = $('#reason').val().trim();
                $('#reject-reason-box').show();

                if (reason === '') {
                    e.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณากรอกเหตุผล',
                        text: 'คุณต้องระบุเหตุผลก่อนจะไม่อนุมัติรายการนี้',
                        confirmButtonText: 'ตกลง',
                    });

                    $('#reason').focus();
                } else {
                    isSubmitting = true;

                    $('#actionInput').val('reject');
                    $('#btnApprove, #btnReject').prop('disabled', true);
                    $('#approveForm').submit();
                }
            });
        });
    </script>
@endsection
