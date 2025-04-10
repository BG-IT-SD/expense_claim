@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Search --}}
        @if (now()->lessThan($approve->token_expires_at))
            <div class="row">
                <!-- Basic Layout -->
                <div class="col-xxl-12">
                    @if(session('message'))
                    {{-- alert --}}
                    <div class="alert alert-{{ session('class') }} alert-dismissible h4" role="alert">
                        <span class="mdi mdi-bell"></span> {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    {{--  alert --}}
                    @endif
                    <div class="card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h3 class="mb-0"><span class="mdi mdi-file-document-check h3"></span> การอนุมัติเลขที่:
                                {{ $expense->prefix . $expense->id }}</h3>
                        </div>
                        <div class="card-body row">

                            <div class="col-sm-6 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="expense_t1" class="form-control"
                                        value="{{ $booking->location_name }}" disabled>
                                    <label for="expense_t1">สถานที่ปฏิบัติงาน</label>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="expense_t2" class="form-control"
                                        value="{{ $departure_date . ' - ' . $return_date }}" disabled>
                                    <label for="expense_t2">วันเวลาที่ออกปฎิบัติงาน</label>
                                </div>
                            </div>
                            {{-- <div class="col-sm-6 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="expense_t1" class="form-control" value="" disabled>
                                    <label for="expense_t1">สถานที่ปฏิบัติงาน</label>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="expense_t1" class="form-control" value="" disabled>
                                    <label for="expense_t1">สถานที่ปฏิบัติงาน</label>
                                </div>
                            </div> --}}
                            <div class="col-sm-12">
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
                                            <td><span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light">{{ $expense->costoffood ?? '0' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ค่าเดินทาง และ อื่นๆ</td>
                                            <td><span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light totaltravel">{{ $expense->travelexpenses ?? '0' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ค่าน้ำมัน</td>
                                            <td><span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light gasolinecost">{{ $expense->gasolinecost ?? '0' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>รวม</td>
                                            <td><span
                                                    class="btn rounded-pill btn-success waves-effect waves-light totalExpense">{{ $expense->totalprice ?? '0' }}</span>
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
                            <div class="col-sm-6 mb-3">
                                <h4><strong>ผู้ขอ:</strong> {{ $expense->empid ?? '-' }}</h4>

                            </div>
                            <div class="col-sm-6 mb-3">
                                <h4> {{ $user->fullname ?? '-' }}</h4>

                            </div>
                            <div class="col-sm-6 mb-3">
                                <h4><strong>สถานะ:</strong> {!! status_approve_badge($approve->statusapprove, $approve->typeapprove) !!}</h4>

                            </div>
                            <div class="col-sm-6 mb-3">
                                <h4><strong>ประเภท:</strong> {!! type_approve_text($approve->typeapprove) !!}</h4>

                            </div>
                            <div class="col-sm-6 mb-3">
                                <h4><strong>ผู้อนุมัติ:</strong> {{ $approve->empid.' | '.$approve->approvename }}</h4>

                            </div>
                            @if ($approve->typeapprove == 4)
                            <div class="col-sm-6 mb-3">
                                <h4><strong>ผู้อนุมัติขั้นถัดไป:</strong> {{ $nextempid.' | '.$nextfullname }}</h4>

                            </div>
                            @endif
                            @if ($approve->statusapprove == 0)
                                <hr>
                                <div class="col-sm-12 mb-3 text-center">

                                    <form id="approveForm" method="POST"
                                        action="{{ route('approve.confirm', $approve->id) }}">
                                        @csrf
                                        <div id="reject-reason-box" class="mb-3" style="display: none;">
                                            <label for="reason" class="form-label">
                                                <h3>เหตุผลที่ไม่อนุมัติ</h3>
                                            </label>
                                            <textarea name="reason" id="reason" class="form-control" rows="3"></textarea>

                                        </div>
                                        <input type="hidden" id="typeapprove" name="typeapprove" value="{{ $approve->typeapprove }}">
                                        <input type="hidden" name="expenseempid" id="expenseempid" value="{{ $expense->empid }}">
                                        <input type="hidden" name="nextempid" id="nextempid" value="{{ $nextempid }}">
                                        <input type="hidden" name="nextfullname" id="nextfullname" value="{{ $nextfullname }}">
                                        <input type="hidden" name="nextemail" id="nextemail" value="{{ $nextemail }}">
                                        <input type="hidden" name="departuredate" id="departuredate" value="{{ $departure_date . ' - ' . $return_date }}">
                                        <input type="hidden" name="approvename" id="approvename" value="{{ $approve->approvename }}">
                                        <input type="hidden" name="expenseid" id="expenseid" value="{{ $expense->prefix . $expense->id }}">
                                        <input type="hidden" name="empemail" id="empemail" value="{{ $expense->user->email }}">
                                        <input type="hidden" name="empfullname" id="empfullname" value="{{ $expense->user->fullname }}">

                                        <button type="button" name="action" value="approve" class="btn btn-success"
                                            id="btnApprove">
                                            <span class="mdi mdi-check-circle"></span> อนุมัติ</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger"
                                            id="btnReject">
                                            <span class="mdi mdi-close-circle-outline"></span> ไม่อนุมัติ</button>
                                        {{-- hidden input ที่จะกำหนดค่า action --}}
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
            // ✅ เมื่อกดปุ่ม "อนุมัติ"
            $('#btnApprove').click(function(e) {
                Swal.fire({
                    icon: 'success',
                    title: 'ยืนยันการอนุมัติ',
                    text: 'คุณต้องการอนุมัติรายการนี้ใช่หรือไม่?',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, อนุมัติ',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#actionInput').val('approve');
                        $('#approveForm').submit();
                    }
                });
            });

            // ❌ ปุ่ม "ไม่อนุมัติ"
            $('#btnReject').click(function(e) {
                const reasonBox = $('#reject-reason-box');
                const reason = $('#reason').val().trim();

                reasonBox.show();

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
                    $('#actionInput').val('reject');
                }
            });
        });
    </script>
@endsection
