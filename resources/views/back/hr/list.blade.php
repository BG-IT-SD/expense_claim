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
            </div>
        </div>
        {{-- End Search --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิก</h5>
                    <div class="table-responsive text-nowrap2">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>Expense ID</th>
                                    <th>Date Time</th>
                                    <th>Booking ID</th>
                                    <th>ID | Name | BU</th>
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
                                            {{ $expense->empid . ' | ' . $expense->user->fullname . ' | ' . $expense->user->bu }}
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
                                                    class="btn btn-sm btn-info"><span
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
                                                        class="btn btn-sm btn-info"><span
                                                            class="mdi mdi-eye-arrow-right-outline"></span> View</a>
                                                @else
                                                    <button class="btn btn-sm btn-warning"
                                                        onclick="window.location.href='{{ route('HR.edit', $expense->id) }}'"><span
                                                            class="mdi mdi-eye-circle-outline"></span> ตรวจสอบ</button>
                                                    {{-- <button class="btn btn-sm btn-danger"><span
                                                    class="mdi mdi-trash-can-outline"></span></button> --}}
                                                @endif
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach


                                {{-- <tr>
                                    <td>EX20241101</td>
                                    <td>09/11/2024 10:30 น.</td>
                                    <td>11080</td>
                                    <td>BGCG</td>
                                    <td><span class="badge rounded-pill bg-label-success me-1">Completed</span></td>
                                    <td><span class="badge rounded-pill bg-label-success me-1">Approved</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><span
                                                class="mdi mdi-plus-box-multiple-outline"></span> </button>
                                        <button class="btn btn-sm btn-warning"><span
                                                class="mdi mdi-eye-circle-outline"></span> Detail</button>
                                        <button class="btn btn-sm btn-danger"><span
                                                class="mdi mdi-trash-can-outline"></span></button>
                                    </td>
                                </tr> --}}

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
    <script>
        $(document).on('click', '.btn-passenger', function() {
            let bookid = $(this).data('bookid');

            $.get(`/HR/passenger-list/${bookid}`, function(res) {
                let html = '';
                res.forEach((item) => {
                    let expenseId = item.expense?.id ?? '-';
                    let empid = item.passenger_empid;
                    let name = item.passenger_name;

                    let last = item.expense?.lastapprove ?? null;
                    let statusText = last?.status_text ?? '-';
                    let typeText = last?.type_text ?? '-';

                    let actionBtn = '';
                    let viewUrl = `/HR/view/${expenseId}/0`; // expenseId มาจาก item.expense?.id

                    if (last) {
                        const type = parseInt(last.typeapprove);
                        const status = parseInt(last.statusapprove);

                        // เงื่อนไขแสดงปุ่ม Approve
                        if ((type === 1 && (status === 1)) || (type === 3 &&
                                status === 0)) {
                            actionBtn = `<button class="btn btn-sm btn-success">✔ APPROVE</button>`;
                        }

                        // เงื่อนไขแสดงปุ่ม View
                        if ((type === 3 && status === 2) || (type === 1 && status === 0) || type >
                            3) {
                            actionBtn = `<a href="${viewUrl}" class="btn btn-sm btn-info">
                    <span class="mdi mdi-eye-arrow-right-outline"></span> View
                 </a>`;
                        }

                        // เงื่อนไขแสดงปุ่ม View
                        if (status === 2) {
                            actionBtn = `<a href="${viewUrl}" class="btn btn-sm btn-info">
                    <span class="mdi mdi-eye-arrow-right-outline"></span> View
                 </a>`;
                        }
                    }

                    html += `
            <tr>
                <td>${expenseId}</td>
                <td>${empid}</td>
                <td>${name}</td>
                <td>${typeText}</td>
                <td>${statusText}</td>
                <td>${actionBtn}</td>
            </tr>`;
                });

                $('#passenger-table-body').html(html);
                $('#modalGroup').modal('show');
            });
        });
    </script>
@endsection
