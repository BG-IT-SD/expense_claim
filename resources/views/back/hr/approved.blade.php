@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Search --}}
        <div class="row">
            <!-- Basic Layout -->
            {{-- <div class="col-xxl">
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
                                        <label class="col-sm-3 col-form-label text-sm-end"
                                            for="formtabs-phone">Status</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="formtabs-phone" class="form-control phone-mask">
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
            </div> --}}
        </div>
        {{-- End Search --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card p-3">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิกที่ตรวจสอบแล้ว</h5>
                    <div class="row">
                        <div class="col-md-6 mt-3 mb-3"></div>
                        <div class="col-md-6 mt-3 mb-3 text-end"> <button type="button" class="btn btn-primary"
                                id="sendSelected">
                                <i class="mdi mdi-content-save"></i> ยืนยันข้อมูลเพื่ออนุมัติ
                            </button></div>
                    </div>

                    <div class="table-responsive text-nowrap2">
                        <table class="table" id="appex">
                            <thead class="table-dark">
                                <tr>
                                    <th><input type="checkbox" id="selectAll" /></th>
                                    <th>Expense ID</th>
                                    <th>Date Time</th>
                                    <th>Booking ID</th>
                                    <th>ID | Name </th>
                                    <th>BU</th>
                                    <th>Location</th>
                                    <th>Type Approve</th>
                                    <th>Approve</th>
                                    {{-- <th>Approve Name</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($expenses as $key => $expense)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="expense_ids[]" class="expense-checkbox"
                                                value="{{ $expense->id }}">
                                        </td>
                                        <td>{{ $expense->prefix . $expense->id }}</td>
                                        <td class="text-wrap">
                                            {{ \Carbon\Carbon::parse($expense->vbooking->departure_date . ' ' . $expense->vbooking->departure_time)->format(
                                                'd/m/Y H:i',
                                            ) .
                                                ' - ' .
                                                \Carbon\Carbon::parse($expense->vbooking->return_date . ' ' . $expense->vbooking->return_time)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>{{ $expense->bookid }}</td>
                                        <td class="text-wrap">
                                            @if ($expense->extype == 2 || $expense->extype == 3)
                                                {{ $expense->empid . ' | ' . $expense->tech->fullname . ' | ' }}
                                            @else
                                                {{ $expense->empid . ' | ' . $expense->user->fullname }}
                                            @endif

                                        </td>
                                        <td class="text-wrap">
                                            {{ BuEmp($expense->empid) }}
                                        </td>
                                        <td>{{ $expense->vbooking->location_name }}</td>
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
                                        {{-- <td >
                                        @if ($expense->latestApprove->statusapprove >= 3)
                                            {{ $expense->$latestApprove->approvename }}
                                        @endif
                                    </td> --}}
                                        <td class="text-nowrap">
                                            @if ($expense->latestApprove->statusapprove == 2)
                                                @if ($expense->extype == 2)
                                                    <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        <span class="mdi mdi-eye-arrow-right-outline"></span> View
                                                    </a>
                                                @else
                                                    <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                        target="_blank" class="btn btn-sm btn-info"><span
                                                            class="mdi mdi-eye-arrow-right-outline"></span> View</a>
                                                @endif
                                            @else
                                                @if ($expense->extype == 2)
                                                    <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        <span class="mdi mdi-eye-arrow-right-outline"></span> View
                                                    </a>
                                                @else
                                                    <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                        target="_blank" class="btn btn-sm btn-info"><span
                                                            class="mdi mdi-eye-arrow-right-outline"></span> View</a>
                                                @endif
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
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
        const hrNextApproveUrl = "{{ route('HR.hrnextapprove') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/approve.js']) }}"></script>
@endsection
