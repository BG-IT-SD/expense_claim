@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4 p-5">
                    <div class="card-header">
                        <h4>
                            เบิกมื้ออาหารของ {{ $driver_empid . ' | ' . $driver_name }}
                        </h4>
                    </div>
                    @php
                        $prices = [1 => 50, 2 => 60, 3 => 60, 4 => 50]; // ราคาแต่ละมื้อ
                    @endphp

                    <div class="row g-4">
                        <form action="{{ route('DriverClaim.store') }}" method="POST">
                            @csrf
                            @foreach ($Alldayfood as $index => $dayFood)
                                @php
                                    $mealchecked_1 = '';
                                    $mealchecked_2 = '';
                                    $mealchecked_3 = '';
                                    $mealchecked_4 = '';

                                    // $dayKey = $dayFood->toDateString();
                                    // $from = $dayFood->copy()->setTime(6, 0);
                                    // $to = $dayFood->copy()->setTime(23, 59);
                                    $dayKey = $dayFood->toDateString();
                                    if (!isset($groupedTimeRanges[$dayKey])) {
                                        continue; // ข้ามวันถ้าไม่มี booking
                                    }
                                    $from = $groupedTimeRanges[$dayKey]['start'];
                                    $to = $groupedTimeRanges[$dayKey]['end'];

                                    if (isset($groupedTimeRanges[$dayKey])) {
                                        $from = $dayFood
                                            ->copy()
                                            ->setTimeFromTimeString(
                                                $groupedTimeRanges[$dayKey]['start']->format('H:i'),
                                            );
                                        $to = $dayFood
                                            ->copy()
                                            ->setTimeFromTimeString($groupedTimeRanges[$dayKey]['end']->format('H:i'));
                                    }

                                    if ($from->hour < 8 || ($to->hour > 6 && $from->hour < 8)) {
                                        $mealchecked_1 = 'checked';
                                    }
                                    if ($from->hour < 17 && $to->hour > 8) {
                                        $mealchecked_2 = 'checked';
                                    }
                                    if ($from->hour < 23 && $to->hour > 17) {
                                        $mealchecked_3 = 'checked';
                                    }
                                    if ($to->hour > 21) {
                                        $mealchecked_4 = 'checked';
                                    }
                                @endphp


                                <div class="col-sm-12">
                                    <div class="card meal-day-box">
                                        <div class="card-body">
                                            <div class="card-header border border-info">
                                                <h5>
                                                    <span class="badge rounded-pill bg-dark">
                                                        <span class="mdi mdi-calendar-month-outline"></span>
                                                        {{-- {{ 'วันที่: ' . $dayFood->toDateString() . ' เวลา: ' . $from->format('H:i') . ' - ' . $to->format('H:i') }} --}}
                                                        วันที่: {{ $dayFood->toDateString() }} เวลา:
                                                        {{ $from->format('H:i') }}
                                                        - {{ $to->format('H:i') }}
                                                    </span>
                                                </h5>
                                                @if (isset($groupedTimeRanges[$dayKey]))
                                                    <div class="text-muted small">
                                                        <strong>Booking ที่เกี่ยวข้อง:</strong>
                                                        <ul>
                                                            @foreach ($groupedTimeRanges[$dayKey]['details'] as $b)
                                                                <li>
                                                                    ID: {{ $b['id'] }} | {{ $b['location_name'] }}
                                                                    @if (isset($b['start']) && isset($b['end']))
                                                                        <br>
                                                                        <span class="text-muted small">
                                                                            เวลาเดินทาง: {{ $b['start']->format('H:i') }} -
                                                                            {{ $b['end']->format('H:i') }}
                                                                        </span>
                                                                    @endif
                                                                    {{-- ปุ่มแก้ไขเวลา --}}
                                                                    {{-- <button type="button"
                                                                        class="btn btn-sm btn-outline-primary ms-2 btnEditBookingTime"
                                                                        data-bookid="{{ $b['id'] }}"
                                                                        data-date="{{ $dayFood->toDateString() }}"
                                                                        data-start="{{ isset($b['start']) ? $b['start']->format('H:i') : '' }}"
                                                                        data-end="{{ isset($b['end']) ? $b['end']->format('H:i') : '' }}"
                                                                        data-location="{{ $b['location_name'] }}"
                                                                        data-action="{{ route('DriverClaim.CarBooking.updateTime', ['bookid' => $b['id']]) }}"
                                                                        >
                                                                        <span class="mdi mdi-clock-edit-outline"></span>
                                                                        แก้ไขเวลา
                                                                    </button> --}}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-bordered text-center">
                                                    <thead>
                                                        <tr class="table-info">
                                                            <th>รายละเอียด</th>
                                                            <th>มื้อเช้า [50 บาท]</th>
                                                            <th>มื้อกลางวัน [60 บาท]</th>
                                                            <th>มื้อเย็น [60 บาท]</th>
                                                            <th>มื้อดึก [50 บาท]</th>
                                                            <th>รวม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <i class="mdi mdi-food-outline text-danger me-2"></i>
                                                                เบิกมื้ออาหาร
                                                                <input type="hidden"
                                                                    name="days[{{ $index }}][date]"
                                                                    value="{{ $dayFood->toDateString() }}">
                                                            </td>

                                                            @for ($i = 1; $i <= 4; $i++)
                                                                <td>
                                                                    <input type="checkbox"
                                                                        name="days[{{ $index }}][meal{{ $i }}][]"
                                                                        class="form-check-input meal-checkbox"
                                                                        data-price="{{ $prices[$i] }}"
                                                                        value="{{ $prices[$i] }}"
                                                                        {{ ${"mealchecked_$i"} }}>
                                                                </td>
                                                            @endfor

                                                            <td>
                                                                <span
                                                                    class="badge rounded-pill bg-label-success me-1 meal-total">0.00</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @foreach ($groupedTimeRanges[$dayKey]['details'] as $detail)
                                    <input type="hidden"
                                        name="groupedTimeRanges[{{ $dayKey }}][details][{{ $loop->index }}][id]"
                                        value="{{ $detail['id'] }}">
                                    <input type="hidden"
                                        name="groupedTimeRanges[{{ $dayKey }}][details][{{ $loop->index }}][location_name]"
                                        value="{{ $detail['location_name'] }}">
                                @endforeach
                            @endforeach


                            {{-- รวมทั้งหมด --}}
                            <div class="row mt-3 mb-3">
                                <div class="col-sm-4">
                                    <input type="hidden" class="expense-value" name="costoffood" id="costoffood"
                                        value="0">
                                    <input type="hidden" class="" name="empid" id="empid"
                                        value="{{ $driver_empid }}">

                                </div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body alert-success row">
                                            <div class="col-md-6 text-end h5">รวม</div>
                                            <div class="col-md-6 text-end grandTotal h5">0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-3 mb-3">
                                <div class="col-md-9">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end"
                                            for="formtabs-first-name">ผู้อนุมัติ</label>
                                        <div class="col-sm-9">
                                            <select name="headapprove" id="headapprove" class="form-select form-select-l">
                                                <option value="{{ $finalIdNext }}">
                                                    {{ $finalHNameNext . ' | ' . $finalHEmailNext }}</option>
                                            </select>
                                            <input type="hidden" name="head_email" id="head_email"
                                                value="{{ $finalHEmailNext }}" class="form-control form-control-input">
                                            <input type="hidden" name="head_name" id="head_name"
                                                value="{{ $finalHNameNext }}" class="form-control form-control-input">
                                            <input type="hidden" name="head_id" id="head_id" value="{{ $finalIdNext }}"
                                                class="form-control form-control-input">
                                            <input type="hidden" name="driver_name" value="{{ $driver_name }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-5 mt-3">
                                <div class="col-md-4"></div>
                                <div class="col-md-4">

                                    {{-- @foreach ($bookingIds as $bookingId)

                                        <input type="text" name="Idbookdiver[]" value=" {{ $bookingId }}">
                                    @endforeach --}}
                                    <button type="button" class="btn btn-primary" id="btnConfirmClaim"><span
                                            class="mdi mdi-content-save-check"></span> ยืนยันการเบิก</button>
                                    <a href="{{ route('DriverClaim.index') }}" class="btn btn-danger">
                                        <span class="mdi mdi-close-outline"></span> ยกเลิก
                                    </a>
                                </div>
                                <div class="col-md-4">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Booking Time -->
{{-- <div class="modal fade" id="modalEditBookingTime" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditBookingTime" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">
                    แก้ไขเวลา Booking: <span id="mBookId"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2 text-muted small">
                    วันที่: <span id="mDate"></span> | สถานที่: <span id="mLocation"></span>
                </div>

                <input type="hidden" name="bookid" id="inputBookId">

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">เวลาเริ่ม (24 ชม)</label>
                        <input
                            type="text"
                            class="form-control"
                            name="start_time"
                            id="inputStart"
                            required
                        >
                    </div>

                    <div class="col-6">
                        <label class="form-label">เวลาสิ้นสุด (24 ชม)</label>
                        <input
                            type="text"
                            class="form-control"
                            name="end_time"
                            id="inputEnd"
                            required
                        >
                    </div>
                </div>

                <div class="form-text mt-2">
                    รูปแบบเวลา: 00:00 – 23:59
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    ยกเลิก
                </button>
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-content-save-outline"></span> บันทึก
                </button>
            </div>
        </form>
    </div>
</div> --}}

@endsection

@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
@endsection

@section('jscustom')
    <script src="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/driver/create.js']) }}"></script>
@endsection
