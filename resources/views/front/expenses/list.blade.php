@extends('layouts.template')
@section('content')
{{-- @dd($userModuleRoles) --}}
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
                                    {{-- <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-phone">Location</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="formtabs-phone" class="form-control phone-mask"
                                                placeholder="" aria-label="658 799 8941">
                                        </div>
                                    </div> --}}
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
                    <div class="table-responsive text-nowrap">
                        <table class="table" id="ExpenseList">
                            <thead class="table-dark">
                                <tr>
                                    <th>Expense ID</th>
                                    <th>Date Time</th>
                                    <th>Booking ID</th>
                                    <th>Location</th>
                                    <th>Type Approve</th>
                                    <th>Approve</th>
                                    <th>Approve Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($booking as $booking)
                                    @php
                                        $expense = optional($booking->expense);
                                        $approve = optional($expense->latestApprove);
                                    @endphp
                                    <tr>
                                        <td>{{ $expense->prefix . $expense->id }}</td>
                                        <td>{{ $booking->departure_date . ' - ' . $booking->return_date }}</td>
                                        <td>{{ $booking->id }}</td>
                                        <td>{{ $booking->location_name }}</td>
                                        <td>
                                            @if (!is_null($approve->typeapprove))
                                                {!! type_approve_text($approve->typeapprove) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!is_null($approve->statusappprove))
                                                {!! status_approve_badge($approve->statusappprove, $approve->typeapprove) !!}
                                            @endif

                                        </td>
                                        <td>
                                            @if (!is_null($approve->approvename))
                                                {{ $approve->approvename }}
                                            @endif
                                        </td>
                                        <td>
                                            {{-- เบิกได้ภายใน 7 วันหลังการเดินทาง --}}
                                            @if (!is_null($expense->id))
                                            <button class="btn btn-sm btn-info"><span class="mdi mdi-eye-arrow-right-outline"></span> View</button>
                                            @else
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="window.location.href='{{ route('Expense.create', $booking->id) }}'"><span
                                                        class="mdi mdi-pencil-circle-outline"></span> เบิก</button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                                {{-- <tr>
                                    <td>-</td>
                                    <td>10/11/2024 10:00 น.</td>
                                    <td>11085</td>
                                    <td>สนามบิน ดอนเมือง</td>
                                    <td><span class="badge rounded-pill bg-label-danger me-1">Inactive</span></td>
                                    <td><span class="badge rounded-pill bg-label-danger me-1">Not Approved</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><span class="mdi mdi-pencil-circle-outline"></span> Edit</button>
                                        <button class="btn btn-sm btn-danger"><span class="mdi mdi-trash-can-outline"></span></button>
                                    </td>
                                </tr> --}}
                                {{-- <tr>
                                    <td>EX20241101</td>
                                    <td>09/11/2024 10:30 น.</td>
                                    <td>11080</td>
                                    <td>BGCG</td>
                                    <td><span class="badge rounded-pill bg-label-success me-1">Completed</span></td>
                                    <td><span class="badge rounded-pill bg-label-success me-1">Approved</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"><span class="mdi mdi-eye-circle-outline"></span> View</button>
                                    </td>
                                </tr> --}}

                            </tbody>
                        </table>
                    </div>
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
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/listexpense.js']) }}"></script>
@endsection
