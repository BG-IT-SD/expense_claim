@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Search --}}
        {{-- <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5>
                    </div>
                    <div class="card-body">

                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-first-name">Expense
                                            ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="formtabs-first-name" class="form-control"
                                                placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-last-name">Booking
                                            ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="formtabs-last-name" class="form-control"
                                                placeholder="">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-birthdate">Date
                                            Time</label>
                                        <div class="col-sm-9">
                                            <input type="hidden" id="formtabs-birthdate"
                                                class="form-control dob-picker flatpickr-input" placeholder="YYYY-MM-DD"
                                                readonly="readonly"><input
                                                class="form-control dob-picker flatpickr-input flatpickr-mobile"
                                                tabindex="1" type="date" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-phone">Emp
                                            ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="formtabs-phone" class="form-control phone-mask"
                                                placeholder="" aria-label="658 799 8941">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-9">
                                            <button type="button"
                                                class="btn btn-primary me-sm-3 me-1 waves-effect waves-light"><span
                                                    class="mdi mdi-file-search-outline"></span></button>
                                            <button type="reset"
                                                class="btn btn-outline-secondary waves-effect">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div> --}}
        {{-- End Search --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิกขออนุมัติ</h5>
                    <form action="{{ route('HeadApprove.confirm') }}" method="POST" id="checkApproveForm">
                        @csrf
                        <div class="table-responsive text-nowrap">


                            <table class="table" id="ExpenseList">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" class="form-check-input" id="checkAll"> เลือกทั้งหมด
                                        </th>
                                        <th>Expense ID</th>
                                        <th>ยอดรวม</th>
                                        <th>Date Time</th>
                                        <th>Booking ID</th>
                                        <th>ID | Name</th>
                                        <th>Location</th>
                                        <th>Type Approve</th>
                                        <th>Approve</th>
                                        <th>Approve Name</th>
                                        <th>Next Step</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($expenses as $key => $expense)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="approve_id[]"
                                                    value="{{ $expense->latestApprove->id }}"
                                                    class="row-checkbox form-check-input">
                                            </td>
                                            <td>{{ $expense->prefix . $expense->id }}</td>
                                            <td>
                                                <h6><span
                                                        class="badge rounded-pill bg-primary">{{ number_format($expense->totalprice, 2) }}</span>
                                                </h6>
                                            </td>
                                            <td class="text-nowrap">
                                                {{ \Carbon\Carbon::parse($expense->vbooking->departure_date . ' ' . $expense->vbooking->departure_time)->format(
                                                    'd/m/Y H:i',
                                                ) .
                                                    ' - ' .
                                                    \Carbon\Carbon::parse($expense->vbooking->return_date . ' ' . $expense->vbooking->return_time)->format('d/m/Y H:i') }}
                                                <input type="hidden" name="departuredate[]"
                                                    value="{{ \Carbon\Carbon::parse($expense->vbooking->departure_date . ' ' . $expense->vbooking->departure_time)->format('d/m/Y H:i') . ' - ' . \Carbon\Carbon::parse($expense->vbooking->return_date . ' ' . $expense->vbooking->return_time)->format('d/m/Y H:i') }}">
                                            </td>
                                            <td>{{ $expense->bookid }}</td>
                                            <td class="text-nowrap">
                                                @if ($expense->extype == 2 || $expense->extype == 3)
                                                    {{ $expense->empid . ' | ' . $expense->tech->fullname }}
                                                    <input type="hidden" name="empfullname[]"
                                                        value="{{ $expense->tech->fullname }}">
                                                @else
                                                    {{ $expense->empid . ' | ' . $expense->user->fullname }}
                                                    <input type="hidden" name="empfullname[]"
                                                        value="{{ $expense->user->fullname }}">
                                                @endif
                                            </td>
                                            <td>{{ $expense->vbooking->location_name }}</td>
                                            <td>
                                                @if (!is_null($expense->latestApprove->typeapprove))
                                                    {!! type_approve_text($expense->latestApprove->typeapprove, $expense->latestApprove->typeapprove) !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (!is_null($expense->latestApprove->statusapprove))
                                                    {!! status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                                                    {{-- {{ $expense->latestApprove->statusapprove.'type=>'.$expense->latestApprove->typeapprove }} --}}
                                                @endif
                                            </td>
                                            <td>
                                                {!! $expense->latestApprove?->approvename ?? '-' !!}
                                            </td>
                                            <td>
                                                @if ($expense->latestApprove->typeapprove == 2)
                                                    @php
                                                        $bu = BuEmp($expense->empid);
                                                        $groupData = $expense->tech->groupapprove ?? 1;
                                                        $nextStepApprove = Approvestep(
                                                            $bu,
                                                            $expense->extype,
                                                            2,
                                                            $groupData,
                                                        );
                                                        $nextempid = $nextStepApprove['empid'] ?? '';
                                                        $nextemail = $nextStepApprove['email'] ?? '';
                                                        $nextfullname = $nextStepApprove['fullname'] ?? '';
                                                    @endphp
                                                    {{ $nextfullname }}
                                                    <input type="hidden" name="nextemail[]" value="{{ $nextemail }}">
                                                    <input type="hidden" name="nextempid[]" value="{{ $nextempid }}">
                                                    <input type="hidden" name="nextfullname[]"
                                                        value="{{ $nextfullname }}">
                                                @endif
                                                <input type="hidden" name="typeapprove[]"
                                                    value="{{ $expense->latestApprove->typeapprove }}">
                                            </td>
                                            <td class="text-nowrap">
                                                @if ($expense->extype == 2)
                                                    <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        <span class="mdi mdi-eye-arrow-right-outline"></span> View
                                                    </a>
                                                @else
                                                    <a href="{{ route('Expense.show', $expense->id) }}"
                                                        class="btn btn-sm btn-info" target="_blank"><span
                                                            class="mdi mdi-eye-arrow-right-outline"></span> View</a>
                                                @endif


                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                        <hr>
                        <div class="mt-3 mb-5 p-3 text-end">
                            <button type="button" id="approveAllBtn" class="btn btn-success">
                                <span class="mdi mdi-check-circle"></span>อนุมัติรายการที่เลือก
                            </button>
                            <button type="button" id="rejectSelected" class="btn btn-danger">
                                <span class="mdi mdi-close-circle"></span>ไม่อนุมัติรายการที่เลือก
                            </button>
                        </div>
                    </form>
                </div>
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
        $(document).ready(function() {
            // Check All
            $('#checkAll').on('click', function() {
                $('.row-checkbox').prop('checked', this.checked);
            });

            // Reject Selected
            $('#rejectSelected').on('click', function() {
                const checked = $('.row-checkbox:checked');
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไม่พบรายการที่เลือก',
                        text: 'กรุณาเลือกรายการที่ต้องการไม่อนุมัติ',
                    });
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการไม่อนุมัติ',
                    text: "คุณแน่ใจหรือไม่ว่าจะไม่อนุมัติรายการที่เลือก?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ไม่อนุมัติ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'action',
                            value: 'reject'
                        }).appendTo('#checkApproveForm');
                        $('#checkApproveForm').submit();
                    }
                });
            });

            // Approve Selected
            $('#approveAllBtn').on('click', function() {
                const checked = $('.row-checkbox:checked');
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไม่พบรายการที่เลือก',
                        text: 'กรุณาเลือกรายการที่ต้องการอนุมัติ',
                    });
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการอนุมัติ',
                    text: "คุณต้องการอนุมัติรายการที่เลือกใช่หรือไม่?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, อนุมัติ',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'action',
                            value: 'approve'
                        }).appendTo('#checkApproveForm');
                        $('#checkApproveForm').submit();
                    }
                });
            });
        });
    </script>
@endsection
