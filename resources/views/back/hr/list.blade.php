@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Search --}}
        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5>
                    </div>
                    <div class="card-body">

                        <form method="GET" action="{{ route('HR.index') }}">
                            <div class="row g-3">
                                {{-- <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="exid">Expense
                                            ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="exid" id="exid" class="form-control"
                                                placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="bookid">Booking
                                            ID</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="bookid" id="bookid" class="form-control"
                                                placeholder="">
                                        </div>
                                    </div>
                                </div> --}}


                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="exdate">Start Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="exdate" name="exdate"
                                                value="{{ request('exdate') }}"
                                                class="form-control dob-picker flatpickr-input" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="end_exdate">End Date</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="end_exdate" name="end_exdate"
                                                value="{{ request('end_exdate') }}"
                                                class="form-control dob-picker flatpickr-input" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="bu">BU</label>
                                        <div class="col-sm-9">
                                            <select name="bu" id="bu" class="form-select">
                                                <option value="" disabled selected>-- เลือกBU --</option>
                                                @foreach ($plants as $key => $plant)
                                                <option value="{{ $plant->plantname }}">
                                                    {{ $plant->plantname }}
                                                </option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="status">Status</label>
                                        <div class="col-sm-9">
                                            <select name="status" id="status" class="form-select">
                                                <option value="" disabled selected>-- เลือกสถานะ --</option>
                                                @foreach ($statusList as $key => $text)
                                                    <option value="{{ $key }}">
                                                        {{ $text }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-9">
                                            <button type="submit"
                                                class="btn btn-primary me-sm-3 me-1 waves-effect waves-light"><span
                                                    class="mdi mdi-file-search-outline"></span></button>
                                            <a href="{{ route('HR.index') }}" class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        {{-- End Search --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิก</h5>
                    <div class="d-flex justify-content-end mb-3 pe-3"> {{-- เพิ่ม padding-end --}}
                        <a href="{{ route('HR.export', request()->query()) }}" target="_blank" class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> EXPORT
                        </a>
                    </div>
                    <div class="table-responsive text-nowrap2">
                        <table class="table" id="listexpense">
                            <thead class="table-dark">
                                <tr>
                                    <th>Expense ID</th>
                                    <th>Date Time</th>
                                    <th>Booking ID</th>
                                    <th>ID | Name</th>
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
                                                {{ $expense->empid . ' | ' . $expense->user->fullname . ' | ' . $expense->user->bu }}
                                            @endif

                                        </td>
                                        <td>
                                            @if ($expense->extype == 2 || $expense->extype == 3)
                                                {{ BuEmp($expense->empid) }}
                                            @else
                                                {{ $expense->user->bu }}
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
                                                <button class="btn btn-sm btn-info btn-passenger" type="button"
                                                    class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modalGroup" data-bookid="{{ $expense->bookid }}"><span
                                                        class="mdi mdi-plus-box-multiple-outline"></span></button>
                                                <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                    target="_blank" class="btn btn-sm btn-info"><span
                                                        class="mdi mdi-eye-arrow-right-outline"></span> View</a>
                                                {{-- <button class="btn btn-sm btn-danger"><span
                                                        class="mdi mdi-trash-can-outline"></span></button> --}}
                                            @else
                                                <button class="btn btn-sm btn-info btn-passenger" type="button"
                                                    class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modalGroup" data-bookid="{{ $expense->bookid }}"><span
                                                        class="mdi mdi-plus-box-multiple-outline"></span></button>
                                                @if ($expense->latestApprove->typeapprove == 1 && $expense->latestApprove->statusapprove == 0)
                                                    <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                        target="_blank" class="btn btn-sm btn-info"><span
                                                            class="mdi mdi-eye-arrow-right-outline"></span> View</a>
                                                @else
                                                    <a href="{{ route('HR.edit', $expense->id) }}" target="_blank"
                                                        class="btn btn-sm btn-warning"><span
                                                            class="mdi mdi-eye-circle-outline"></span> ตรวจสอบ</a>
                                                    {{-- <button class="btn btn-sm btn-warning"
                                                        onclick="window.location.href='{{ route('HR.edit', $expense->id) }}'"><span
                                                            class="mdi mdi-eye-circle-outline"></span> ตรวจสอบ</button> --}}
                                                    {{-- <button class="btn btn-sm btn-danger"><span
                                                    class="mdi mdi-trash-can-outline"></span></button> --}}
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
    <div class="modal fade" id="modalGroup" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">List Group Book ID : <span id="bookid-title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>EXPENSE ID</th>
                                <th>EMP ID</th>
                                <th>NAME</th>
                                <th>TYPE</th>
                                <th>STATUS</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="passenger-table-body">
                            <!-- loaded by ajax -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">CLOSE</button>
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
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/hrapprove.js']) }}"></script>
@endsection
