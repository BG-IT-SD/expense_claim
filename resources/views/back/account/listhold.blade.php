@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">
                {{--  --}}
                <form id="holdApproveForm" action="{{ route('Account.HoldApprove.confirm') }}" method="POST">
                    @csrf
                    <div class="card p-3">
                        <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิกที่ Hold</h5>
                        <div class="row">
                            <div class="col-md-6 mt-3 mb-3">
                                <label for="paymentdate">วันที่จ่าย</label>
                                <input type="text" id="paymentdate" name="paymentdate"
                                    class="form-control dob-picker flatpickr-input">
                            </div>
                            <div class="col-md-6 mt-3 mb-3 text-end"> <button type="button" class="btn btn-primary"
                                    id="confirmApproveBtn">
                                    <i class="mdi mdi-content-save"></i> ยืนยันข้อมูลเพื่ออนุมัติ
                                </button></div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table" id="holdApproveForm">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" /></th>
                                        <th>Expense ID</th>
                                        <th>Date Time</th>
                                        {{-- <th>Booking ID</th> --}}
                                        <th>ID | Name </th>
                                        <th>BU</th>
                                        <th>Location</th>
                                        <th>ค่าอาหาร</th>
                                        <th>ค่าน้ำมัน</th>
                                        <th>ค่าทางด่วน</th>
                                        <th>ค่ารถโดยสารสาธารณะ</th>
                                        <th>ค่าใช้จ่ายอื่นๆ</th>
                                        <th>รวมเบิก</th>
                                        <th>Type Approve</th>
                                        <th>Approve</th>
                                        {{-- <th>Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($expenses as $key => $expense)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="expense_ids[]" class="row-checkbox"
                                                    value="{{ $expense->id }}">
                                            </td>
                                            <td>{{ $expense->prefix . $expense->id }}</td>
                                            <td class="text-nowrap">
                                                {{ \Carbon\Carbon::parse($expense->vbooking->departure_date . ' ' . $expense->vbooking->departure_time)->format(
                                                    'd/m/Y H:i',
                                                ) .
                                                    ' - ' .
                                                    \Carbon\Carbon::parse($expense->vbooking->return_date . ' ' . $expense->vbooking->return_time)->format('d/m/Y H:i') }}
                                            </td>
                                            {{-- <td>{{ $expense->bookid }}</td> --}}
                                            <td class="text-nowrap">
                                                @if ($expense->extype == 2 || $expense->extype == 3)
                                                    {{ $expense->empid . ' | ' . $expense->tech->fullname . ' | ' }}
                                                    <input type="hidden" name="fullname[]"
                                                        value="{{ $expense->tech->fullname }}">
                                                @else
                                                    {{ $expense->empid . ' | ' . $expense->user->fullname }}
                                                    <input type="hidden" name="fullname[]"
                                                        value="{{ $expense->user->fullname }}">
                                                @endif
                                                <input type="hidden" name="empemail[]"
                                                    value="{{ EmailEmp($expense->empid) }}">
                                            </td>
                                            <td class="text-nowrap">
                                                {{ BuEmp($expense->empid) }}
                                            </td>
                                            <td>{{ $expense->vbooking->locationbu }}</td>
                                            <td>{{ $expense->costoffood ?? 0 }}
                                                <input type="hidden" name="costoffood[]"
                                                    value="{{ $expense->costoffood ?? 0 }}">
                                            </td>
                                            <td>{{ $expense->gasolinecost ?? 0 }}
                                                <input type="hidden" name="gasolinecost[]"
                                                    value="{{ $expense->gasolinecost ?? 0 }}">
                                            </td>
                                            <td>{{ $expense->expresswaytoll ?? 0 }}
                                                <input type="hidden" name="expresswaytoll[]"
                                                    value="{{ $expense->expresswaytoll ?? 0 }}">
                                            </td>
                                            <td>{{ $expense->publictransportfare ?? 0 }}
                                                <input type="hidden" name="publictransportfare[]"
                                                    value="{{ $expense->publictransportfare ?? 0 }}">
                                            </td>
                                            <td>{{ $expense->otherexpenses ?? 0 }}
                                                <input type="hidden" name="otherexpenses[]"
                                                    value="{{ $expense->otherexpenses ?? 0 }}">
                                            </td>
                                            <td>{{ $expense->totalprice ?? 0 }}
                                                <input type="hidden" name="totalprice[]"
                                                    value="{{ $expense->totalprice ?? 0 }}">
                                                <input type="hidden" name="travelexpenses[]"
                                                    value="{{ $expense->travelexpenses ?? 0 }}">
                                            </td>
                                            <td>
                                                @if (!is_null($expense->latestApprove->typeapprove))
                                                    {!! type_approve_text($expense->latestApprove->typeapprove, $expense->latestApprove->typeapprove) !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (!is_null($expense->latestApprove->statusapprove))
                                                    {!! hr_status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                                                    {{-- {{ $expense->latestApprove->statusapprove.'type=>'.$expense->latestApprove->typeapprove }} --}}
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                @if ($expense->extype == 2)
                                                    {{-- <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                            target="_blank" class="btn btn-sm btn-info">
                                                            <span class="mdi mdi-eye-arrow-right-outline"></span> View
                                                        </a> --}}
                                                @else
                                                    {{-- <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                            target="_blank" class="btn btn-sm btn-info"><span
                                                                class="mdi mdi-eye-arrow-right-outline"></span> View</a> --}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal --}}
@endsection
@section('jscustom')
    @if (session('message'))
        <script>
            Swal.fire({
                title: {!! json_encode(session('message')) !!}, // ป้องกัน Error ใน JavaScript
                icon: {!! json_encode(session('class')) !!},
                customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                },
                buttonsStyling: false
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#paymentdate').flatpickr({
                defaultDate: "today",
                monthSelectorType: 'static',
            });

            // Check All
            $('#selectAll').on('click', function() {
                $('.row-checkbox').prop('checked', this.checked);
            });

            $('#confirmApproveBtn').on('click', function() {
                const checked = $('.row-checkbox:checked');

                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไม่พบรายการที่เลือก',
                        text: 'กรุณาเลือกรายการที่อยู่ในสถานะ Hold เพื่อดำเนินการอนุมัติ',
                    });
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการอนุมัติรายการที่ Hold?',
                    text: "คุณต้องการอนุมัติรายการที่เลือกใช่หรือไม่?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, อนุมัติ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#holdApproveForm').submit();
                    }
                });
            });
        });
    </script>

    {{-- <script>
        const hrNextApproveUrl = "{{ route('HR.hrnextapprove') }}";
    </script> --}}
    {{-- <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/approve.js']) }}"></script> --}}
@endsection
