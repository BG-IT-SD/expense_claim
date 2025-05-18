@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Search --}}
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
                        <h3 class="mb-0"><span class="mdi mdi-file-document-check h3"></span>
                            รายการขอเบิกเบี้ยเลี้ยงและค่าเดินทางกลุ่ม:
                            {{ ' EXGROUP-' . $exgroup->id . ' วันที่ ' . $exgroup->groupdate }}</h3>
                    </div>
                    <div class="card-body row">
                        <form id="approveForm" action="{{ route('Account.exgroup.approve') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="paymentdate">วันที่จ่าย</label>
                                    <input type="text" id="paymentdateview" name="paymentdate"
                                        class="form-control dob-picker flatpickr-input" value="{{ $exgroup->paymentdate }}"
                                        disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="mb-1">ดาวน์โหลดเอกสาร</label>
                                    <br>
                                    <a href="#" class="btn btn-sm btn-danger" target="_blank">
                                        <span class="mdi mdi-file-pdf-box"></span>
                                    </a>
                                    <a href="{{ route('Account.export.group.excel', $exgroup->id) }}" class="btn btn-sm btn-success"  target="_blank">
                                        <span class="mdi mdi-file-excel"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive text-nowrap">

                                <table class="table table-bordered text-center lock-table">
                                    <thead class="table-secondary">
                                        <tr>
                                            {{-- <th><input type="checkbox" id="selectAll" /></th> --}}
                                            <th class="sticky-col">Action</th>
                                            {{-- <th>ลำดับ</th> --}}
                                            <th>EXID</th>
                                            <th>สถานที่ไปปฏิบัติงาน</th>
                                            <th>บริษัท</th>
                                            <th>รหัสพนักงาน</th>
                                            <th>ชื่อ – นามสกุล</th>
                                            {{-- <th>หน่วยงาน</th> --}}
                                            {{-- <th>ระดับ</th>
                                        <th>เลขบัญชี</th> --}}
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
                                                {{-- <td>
                                                <input type="checkbox" name="expense_ids[]" class="expense-checkbox"
                                                value="{{ $expense->id }}">
                                            </td> --}}
                                                <td class="sticky-col">
                                                    @if (!is_null($expense->latestApprove->statusapprove))
                                                        {!! status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                                                    @endif
                                                </td>
                                                {{-- <td>
                                                {{ $i + 1 }}
                                            </td> --}}
                                                <td> {{ 'EX' . $expense->id }}
                                                    <input type="hidden" name="expense_id[]" value="{{ $expense->id }}">
                                                </td>
                                                <td>{{ $expense->vbooking->locationbu }}</td>
                                                <td>{{ BuEmp($expense->empid) }}</td>
                                                <td>{{ $expense->empid }}</td>
                                                <td class="text-start">{{ $fullname }}
                                                    <input type="hidden" name="fullname[]" value="{{ $fullname }}">
                                                </td>
                                                {{-- <td>{{ $expense->userhr->DEPT ?? '-' }}</td> --}}
                                                {{-- <td>{{ $expense->userhr->NUMLVL ?? '-' }}</td>
                                            <td>{{ $expense->userhr->NUMBANK ?? '-' }}</td> --}}
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
                                            <input type="hidden" class="row-food" data-id="{{ $expense->id }}"
                                                value="{{ $food }}">
                                            <input type="hidden" class="row-gas" data-id="{{ $expense->id }}"
                                                value="{{ $gas }}">
                                            <input type="hidden" class="row-express" data-id="{{ $expense->id }}"
                                                value="{{ $express }}">
                                            <input type="hidden" class="row-public" data-id="{{ $expense->id }}"
                                                value="{{ $publictransport }}">
                                            <input type="hidden" class="row-other" data-id="{{ $expense->id }}"
                                                value="{{ $other }}">
                                        @endforeach

                                        <tr class="table-warning fw-bold">
                                            <td colspan="9">รวม</td>
                                            <td>{{ number_format($sum_food, 2) }}

                                            </td>
                                            <td>{{ number_format($sum_gas, 2) }}

                                            </td>
                                            <td>{{ number_format($sum_express, 2) }}
                                                <input type="hidden" name="netexpresswaytoll" class="row-sum_express"
                                                    data-id="{{ $expense->id }}" value="{{ $sum_express }}">

                                            </td>
                                            <td>{{ number_format($sum_publictransport, 2) }}
                                                <input type="hidden" name="netpublictransportfare"
                                                    class="row-sum_publictransport" data-id="{{ $expense->id }}"
                                                    value="{{ $sum_publictransport }}">

                                            </td>
                                            <td>{{ number_format($sum_other, 2) }}
                                                <input type="hidden" name="netotherexpenses" class="row-sum_other"
                                                    data-id="{{ $expense->id }}" value="{{ $sum_other }}">

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
                                            <th>
                                                <h6>จำนวนเงินเบิกได้ / บาท</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>ค่าอาหาร</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light sum-meal">
                                                    {{ number_format($exgroup->totalfood, 2) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-info waves-effect waves-light sum-mealnet">
                                                    {{ number_format($exgroup->nettotalfood, 2) }}</span>
                                                <input type="hidden" name="nettotalfood" class="row-foodnet"
                                                    data-id="{{ $expense->id }}"
                                                    value="{{ number_format($exgroup->nettotalfood, 2) }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ค่าเดินทาง และ อื่นๆ</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light totaltravel">{{ number_format($exgroup->totalother, 2) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-info waves-effect waves-light totaltravelnet">{{ number_format($exgroup->nettotalother, 2) }}</span>
                                                <input type="hidden" name="nettotalother" class="row-othernet"
                                                    data-id="{{ $expense->id }}"
                                                    value="{{ number_format($exgroup->nettotalother, 2) }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ค่าน้ำมัน</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-primary waves-effect waves-light gasolinecost">{{ number_format($exgroup->totalfuel, 2) }}</span>

                                            </td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-info waves-effect waves-light gasolinecostnet">{{ number_format($exgroup->nettotalfuel, 2) }}</span>
                                                <input type="hidden" name="nettotalfuel" class="row-gasnet"
                                                    data-id="{{ $expense->id }}"
                                                    value="{{ number_format($exgroup->nettotalfuel, 2) }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>รวม</td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-success waves-effect waves-light totalExpense">
                                                    {{ number_format($exgroup->total, 2) }}</span>

                                            </td>
                                            <td>
                                                <span
                                                    class="btn rounded-pill btn-success waves-effect waves-light totalExpenseNet">
                                                    {{ number_format($exgroup->nettotal, 2) }}</span>
                                                <input type="hidden" name="nettotal" class="row-totalexNet"
                                                    data-id="{{ $expense->id }}"
                                                    value="{{ number_format($exgroup->nettotal, 2) }}">
                                            </td>
                                        </tr>

                                        {{-- <tr>
                                        <td>รวมที่ต้องเบิกจ่าย</td>
                                        <td>
                                            <span
                                                class="btn rounded-pill btn-success waves-effect waves-light totalExpenseNet">0</span>
                                        </td>
                                    </tr> --}}

                                    </tbody>
                                </table>
                                <hr>
                                <div class="text-center row">
                                    <div class="col-md-12 mb-3">
                                        <h5>ผู้อนุมัติ :
                                            {{ $exgroup->accountempid . ' | ' . $exgroup->accountuser->fullname }}</h5>
                                    </div>

                                </div>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
@endsection
@section('csscustom')
    <style>
        .lock-table {
            border-collapse: collaps;
            width: max-content;
        }

        .lock-table th,
        .lock-table td {
            padding: 8px 16px;
            border: 1px solid #ddd;
            white-space: nowrap;
        }

        .sticky-col {
            position: sticky;
            left: 0;
            background: #ffffff;
            z-index: 1;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
@endsection
@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
@endsection
