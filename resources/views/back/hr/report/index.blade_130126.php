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

                        <form method="GET" action="{{ route('HR.reporthr') }}">
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
                                            <a href="{{ route('HR.reporthr') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <a href="#" target="_blank" class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> EXPORT
                        </a>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table" id="listexpense">
                            <thead class="table-dark">
                                <tr>
                                    <th>บริษัท</th>
                                    <th>รหัสพนักงาน</th>
                                    <th>ชื่อ - นามสกุล</th>
                                    <th>หน่วยงาน</th>
                                    <th>ระดับ</th>
                                    <th>เลขบัญชี</th>
                                    <th>Booking ID</th>
                                    <th>ประเภทรถ</th>
                                    <th>จำนวนผู้โดยสาร</th>
                                    <th>Status การเดินทาง</th>
                                    <th>ชื่อผู้ขับ</th>
                                    <th>สถานที่ต้นทาง</th>
                                    <th>สถานที่ไปปฏิบัติงาน</th>
                                    <th>จำนวนระยะทางที่ 1</th>
                                    <th>จำนวนระยะทางที่ 2</th>
                                    <th>รวมระยะทาง</th>
                                    {{-- <th>Status การเบิก</th> --}}
                                    <th>Expense ID</th>
                                    <th>จากวันที่</th>
                                    <th>เวลาออกเดินทาง</th>
                                    <th>ถึงวันที่</th>
                                    <th>เวลาที่ถึง</th>
                                    <th>จำนวนวัน</th>
                                    <th>ค่าเบี้ยเลี้ยง / อาหาร</th>
                                    <th>ค่าน้ำมัน</th>
                                    <th>ค่าทางด่วน</th>
                                    <th>ค่ารถโดยสารสาธารณะ</th>
                                    <th>ค่าใช้จ่ายอื่นๆ</th>
                                    <th>Total</th>
                                    <th>ประเภทอนุมัติ</th>
                                    <th>สถานะการอนุมัติ</th>
                                    <th>ผู้อนุมัติล่าสุด</th>
                                    <th>ผู้อนุมัติลำดับถัดไป</th>
                                    <th>หมายเหตุ</th>
                                    <th>วันที่จ่าย</th>


                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                                {{-- 1) ยังไม่ค้นหา (ยังไม่กรอกวันที่) --}}
                                @if (!request()->filled('exdate') || !request()->filled('end_exdate'))
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            กรุณาเลือกช่วงวันที่ (Start Date / End Date) แล้วกดค้นหา
                                        </td>
                                    </tr>

                                    {{-- 2) ค้นหาแล้วแต่ไม่พบข้อมูล --}}
                                @elseif ($expenses->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา
                                        </td>
                                    </tr>

                                    {{-- 3) มีข้อมูล --}}
                                @else
                                    @foreach ($expenses as $expense)
                                        @php
                                            $food = $expense->costoffood ?? 0; //ค่าอาหาร
                                            $gas = $expense->gasolinecost ?? 0; // ค่าน้ำมัน
                                            $express = $expense->expresswaytoll ?? 0; //ค่าทางด่วน
                                            $publictransport = $expense->publictransportfare ?? 0; //ค่ารถสาธารณะ
                                            $other = $expense->otherexpenses ?? 0; // ค่าใช้จ่ายอื่นๆ
                                            $total = $food + $gas + $express + $publictransport + $other;

                                            $fullname = in_array($expense->extype, [2, 3])
                                                ? optional($expense->tech)->fullname
                                                : optional($expense->user)->fullname;

                                            $total_distance =
                                                ($expense->totaldistance ?? 0) + ($expense->distancemore ?? 0);

                                            // เลือก booking
                                            $booking =
                                                $expense->extype == 2 ? $expense->vbookingdrv : $expense->vbooking;

                                            $startDate = optional($booking)->departure_date;
                                            $endDate = null;
                                            $endTime = null;

                                            if ($expense->extype == 2) {
                                                $logs = $expense->logs;
                                                $lastLog = $logs->sortByDesc('id')->first();

                                                if ($lastLog) {
                                                    // หา date
                                                    if (preg_match('/\d{4}-\d{2}-\d{2}/', $lastLog->remark, $m)) {
                                                        $endDate = $m[0];
                                                    } else {
                                                        $endDate = optional($booking)->return_date; // fallback
                                                    }

                                                    // หา time (รองรับ HH:MM หรือ HH:MM:SS)
                                                    if (
                                                        preg_match('/\b\d{2}:\d{2}(?::\d{2})?\b/', $lastLog->remark, $t)
                                                    ) {
                                                        $endTime = $t[0];
                                                    }
                                                }

                                                // fallback เวลา ถ้าไม่มีใน log
                                                if (!$endTime) {
                                                    $endTime = optional($expense->vbookingreport)->return_time ?? null;
                                                }
                                            } else {
                                                $endDate = optional($booking)->return_date;
                                                $endTime =
                                                    $expense->returntime ??
                                                    (optional($expense->vbookingreport)->return_time ?? null);
                                            }

                                            $days =
                                                $startDate && $endDate
                                                    ? \Carbon\Carbon::parse($startDate)->diffInDays(
                                                            \Carbon\Carbon::parse($endDate),
                                                            true,
                                                        ) + 1
                                                    : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if ($expense->extype == 2 || $expense->extype == 3)
                                                    {{ optional($expense->tech)->bu ?? '-' }}
                                                @else
                                                    {{ optional($expense->user)->bu ?? '-' }}
                                                @endif

                                            </td>
                                            <td>
                                                {{ $expense->empid }}
                                            </td>
                                            <td>
                                                {{ $fullname }}

                                            </td>

                                            <td>{{ $expense->userhr->DEPT ?? '-' }}</td>

                                            <td>
                                                {{ $expense->userhr->JOBGRADE_TITLE ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $expense->userhr->NUMBANK ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $expense->bookid }}
                                            </td>
                                            @php
                                                $cartype = optional($expense->vbookingreport)->car_status ?? '';
                                            @endphp
                                            <td>
                                                {{ optional($expense->vbookingreport)->title
                                                    ? optional($expense->vbookingreport)->title . (filled($cartype) ? ' (' . $cartype . ')' : '')
                                                    : '-' }}
                                            </td>

                                            <td>{{ optional($expense->vbookingreport)->passengers ?? 0 }}</td>
                                            <td>
                                                {{-- สถานะการเดินทาง ผู้ขอ หรือ ผู้ร่วมทาง --}}
                                                {{ optional($expense->vbookingreport)->person_type ?? '' }}
                                            </td>
                                            <td>
                                                {{-- ผู้ขับ --}}
                                                {{ optional($expense->vbookingreport)->driver_name ?? $fullname }}
                                            </td>
                                            <td>
                                                {{-- สถานีต้นทาง --}}
                                                {{ $expense->departurefrom == 2 ? ($expense->map_a_name ?: '-') : (optional($expense->vbookingreport)->bu ?: '-') }}

                                            </td>
                                            <td>{{ optional($expense->vbookingreport)->display_location ?? (optional($expense->vbookingreport)->location_name ?? '-') }}
                                            </td>
                                            <td>{{ $expense->totaldistance ?? 0.0 }}</td>
                                            <td>{{ $expense->distancemore ?? 0.0 }}</td>
                                            <td>
                                                {{ number_format($total_distance, 2) }}
                                            </td>
                                            {{-- <td>ปกติ</td> --}}
                                            <td>{{ 'EX' . $expense->id }}</td>
                                            <td>{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                @if (filled($expense->departuretime))
                                                    {{ $expense->departuretime }}
                                                @else
                                                    {{ optional($expense->vbookingreport)->departure_time ?? '-' }}
                                                @endif

                                            </td>
                                            <td>{{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td> {{ $endTime }}</td>
                                            <td> {{ $days }}</td>
                                            <td> {{ number_format($food, 2) }}</td>
                                            <td> {{ number_format($gas, 2) }}</td>
                                            <td>{{ number_format($express, 2) }}</td>
                                            <td>{{ number_format($publictransport, 2) }}</td>
                                            <td>{{ number_format($other, 2) }}</td>
                                            <td>{{ number_format(round($expense->totalprice), 2) }}</td>
                                            <td>
                                                @if (!is_null($expense->latestApprove->typeapprove))
                                                    {!! type_approve_text($expense->latestApprove->typeapprove, $expense->latestApprove->typeapprove) !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (!is_null($expense->latestApprove->statusapprove))
                                                    {!! hr_status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                                                @endif
                                            </td>
                                            {{-- <td>{!! $expense->latestApprove?->approvename ?? '-' !!}</td> --}}
                                            <td>{{ $expense->approve_cur ?? '-' }}</td>
                                            <td>
                                                {{-- อนุมัติ nextstep --}}
                                                {{-- {{ $expense->nextApprover['fullname'] ?? '-' }} --}}
                                                {{ $expense->approve_next ?? '-' }}

                                            </td>
                                            <td>{{ optional($expense->latestApprove)->remark ?? '' }}</td>
                                            <td>{{ optional($expense->exgroupData)->paymentdate ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('jscustom')
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/reporthr.js']) }}"></script>
@endsection
