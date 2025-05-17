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
                                                \Carbon\Carbon::parse($expense->vbooking->departure_date)->diffInDays(
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

                        <div class="row text-center mt-5">
                            <div class="col-md-4 card shadow-none bg-transparent border border-primary mb-3">

                                <h5 class="card-header">ผู้จัดทำ</h5>
                                <div class="card-body">
                                    <h5><span class="badge rounded-pill bg-primary"> {{ $create_by }}</span></h5>
                                    <hr>
                                    HR
                                </div>

                            </div>
                            <div class="col-md-4 card shadow-none bg-transparent border border-primary mb-3">

                                <h5 class="card-header">ผู้ตรวจสอบ</h5>
                                <div class="card-body">
                                    <h5><span class="badge rounded-pill bg-primary">{{ $checked_by }}</span></h5>
                                    <hr>
                                    HR
                                </div>

                            </div>
                            <div class="col-md-4 card shadow-none bg-transparent border border-primary mb-3">

                                <h5 class="card-header">ผู้อนุมัติ</h5>
                                <div class="card-body">
                                    <h5><span class="badge rounded-pill bg-primary">{{ $finaluser }}</span>
                                    </h5>
                                    <hr>
                                    HR
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
