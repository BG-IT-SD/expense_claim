@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4 p-5">
                    <div class="card-header">
                        <h4>
                            รายการเบิกมื้ออาหารของ {{ $driver_empid . ' | ' . $driver_name }}
                        </h4>
                        <h5><span class="badge rounded-pill bg-primary"><span class="mdi mdi-file-multiple"></span> {{ $expense->prefix.$expense->id }}</span></h5>
                    </div>
                    @php
                        $prices = [1 => 50, 2 => 60, 3 => 60, 4 => 50]; // ราคาแต่ละมื้อ
                    @endphp

                    <div class="row g-4">
                        <form action="{{ route('HR.claimdriverupdate', $expense->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @foreach ($Alldayfood as $index => $dayFood)
                                @php
                                    $dayKey = $dayFood->toDateString();
                                    $food = $expense->foods->firstWhere('used_date', $dayKey); // ดึงข้อมูลเบิกของวันนั้น
                                    $from = $groupedTimeRanges[$dayKey]['start'];
                                    $to = $groupedTimeRanges[$dayKey]['end'];
                                @endphp

                                <div class="col-sm-12">
                                    <div class="card meal-day-box">
                                        <div class="card-body">
                                            <div class="card-header border border-info">
                                                <h5>
                                                    <span class="badge rounded-pill bg-dark">
                                                        <span class="mdi mdi-calendar-month-outline"></span>
                                                        วันที่: {{ $dayKey }} เวลา: {{ $from->format('H:i') }} -
                                                        {{ $to->format('H:i') }}
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
                                                                <input type="hidden" name="days[{{ $index }}][date]"
                                                                    value="{{ $dayKey }}">
                                                            </td>

                                                            @php
                                                                $total = 0;
                                                                $prices = [1 => 50, 2 => 60, 3 => 60, 4 => 50];
                                                            @endphp

                                                            @for ($i = 1; $i <= 4; $i++)
                                                                @php
                                                                    $meal = optional($food)['meal' . $i] ?? 0;
                                                                    $checked = $meal > 0 ? 'checked' : '';
                                                                    $total += $meal;
                                                                @endphp
                                                            <td>
                                                                {{-- ซ่อนค่า 0 ถ้าไม่ได้ติ๊ก --}}
                                                                <input type="hidden" name="days[{{ $index }}][meal{{ $i }}][]" value="0">

                                                                <input type="checkbox"
                                                                    class="form-check-input meal-checkbox"
                                                                    data-price="{{ $prices[$i] }}"
                                                                    value="{{ $prices[$i] }}"
                                                                    name="days[{{ $index }}][meal{{ $i }}][]"
                                                                    {{ $checked }}
                                                                    @if ($type == 0) onclick="return false;" @endif>
                                                            </td>
                                                            @endfor

                                                            <td>
                                                                <span class="badge bg-label-success me-1">
                                                                    {{ number_format($total, 2) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach



                            {{-- รวมทั้งหมด --}}
                            <div class="row mt-3 mb-3">
                                <div class="col-sm-4">
                                    <input type="hidden" class="expense-value" name="costoffood" id="costoffood"
                                        value="{{ $expense->costoffood }}">
                                    <input type="hidden" class="" name="empid" id="empid"
                                        value="{{ $driver_empid }}">

                                </div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-body alert-success row">
                                            <div class="col-md-6 text-end h5">รวม</div>
                                            <div class="col-md-6 text-end grandTotal h5">
                                                {{ number_format($expense->costoffood, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Apporve Timeline --}}
                            <div class="row g-4">
                                <div class="col-sm-12">
                                    <div class="card shadow-none bg-transparent border border-primary mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title "><span
                                                    class="badge rounded-pill bg-primary">สถานะลำดับการอนุมัติ</span>
                                            </h5>
                                            {{-- <hr> --}}
                                            <div class="timeline-horizontal">
                                                @foreach ($approvals as $index => $item)
                                                    <div class="timeline-step">
                                                        <div class="circle">
                                                            {{ $index + 1 }}
                                                        </div>
                                                        <div class="label">{!! type_approve_text($item->typeapprove) !!}</div>
                                                        <div class="approver">{{ $item->approvename }}</div>
                                                        <div class="status-badge">
                                                            @if ($item->statusapprove == 1)
                                                                <span class="badge bg-success">อนุมัติแล้ว</span>
                                                            @elseif ($item->statusapprove == 2)
                                                                <span class="badge bg-danger">ไม่อนุมัติ</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">รออนุมัติ</span>
                                                            @endif
                                                        </div>
                                                        <div class="timestamp">
                                                            {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Apporve Timeline --}}
                            @if ($type == 1)
                                {{--  --}}
                                <div class="card shadow-none bg-transparent border border-primary mb-3">
                                    <div class="card-body">
                                        <div class="row mt-3 mb-3 justify-content-center text-center ">
                                            <div class="col-md-10">
                                                <label class="form-label fw-bold">ผู้ตรวจสอบ</label>
                                                <select name="headapprove" id="headapprove" class="form-select text-center">
                                                    <option value="{{ $headempid }}">
                                                        {{ $headname . ' | ' . $heademail }}
                                                    </option>
                                                </select>
                                            </div>
                                            {{-- <div class="col-md-5">
                                                <label class="form-label fw-bold">ผู้อนุมัติขั้นถัดไป</label>
                                                <select name="nextapprove" id="nextapprove" class="form-select text-center">
                                                    <option value="{{ $finalIdNext }}">
                                                        {{ $finalHNameNext . ' | ' . $finalHEmailNext }}
                                                    </option>
                                                </select>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>


                                <input type="hidden" name="head_email" id="head_email" value="{{ $heademail }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="head_name" id="head_name" value="{{ $headname }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="head_id" id="head_id" value="{{ $headempid }}"
                                    class="form-control form-control-input">

                                <input type="hidden" name="nexthead_email" id="finalHEmailNext"
                                    value="{{ $finalHEmailNext }}" class="form-control form-control-input">
                                <input type="hidden" name="nexthead_name" id="finalHNameNext"
                                    value="{{ $finalHNameNext }}" class="form-control form-control-input">
                                <input type="hidden" name="nexthead_id" id="finalIdNext" value="{{ $finalIdNext }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="driver_name" value="{{ $driver_name }}">
                                <input type="hidden" name="departuredatemail" value="{{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}">
                                <input type="hidden" name="empfullname" value="{{ $driver_name }}">




                                <div class="row mb-5 mt-3">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-primary" id="btnConfirmClaim"><span
                                                class="mdi mdi-content-save-check"></span> ตรวจสอบข้อมูลการเบิก</button>
                                        <a href="{{ route('HR.hrdriver') }}" class="btn btn-danger">
                                            <span class="mdi mdi-close-outline"></span> ยกเลิก
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                </div>
                                {{--  --}}
                            @else
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jscustom')
    <script>
        function updateMealTotal() {
            let grandTotal = 0;
            $('.meal-day-box').each(function() {
                let rowTotal = 0;
                $(this).find('.meal-checkbox').each(function() {
                    if ($(this).is(':checked')) {
                        rowTotal += parseFloat($(this).data('price')) || 0;
                    }
                });
                $(this).find('.meal-total').text(rowTotal.toFixed(2));
                grandTotal += rowTotal;
            });
            $('.grandTotal').text(grandTotal.toFixed(2));
            $('#costoffood').val(grandTotal.toFixed(2));
        }

        // call on load
        $(document).ready(function() {
            updateMealTotal();
            $('#btnConfirmClaim').on('click', function() {
                Swal.fire({
                    title: 'ตรวจสอบข้อมูลการเบิก?',
                    text: 'คุณแน่ใจว่าต้องการตรวจสอบข้อมูลการเบิกรายการอาหาร?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ใช่, ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('form').submit();
                    }
                });
            });
        });

        // bind event
        $(document).on('change', '.meal-checkbox', updateMealTotal);
    </script>
@endsection

@section('csscustom')
    <style>
        /* timeline approve */
        .timeline-horizontal {
            position: relative;
            display: flex;
            justify-content: flex-start;
            /* 👈 เปลี่ยนจาก space-between เป็น flex-start */
            align-items: flex-start;
            flex-wrap: nowrap;
            gap: 50px;
            /* เพิ่มระยะห่างระหว่างจุด */
            padding: 30px 10px;
        }

        .timeline-horizontal::before {
            content: "";
            position: absolute;
            top: 40px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #ccc;
            z-index: 0;
        }

        .timeline-step {
            position: relative;
            text-align: center;
            z-index: 1;
            flex: 1;
            min-width: 140px;
        }

        .timeline-step .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0 auto 8px;
            line-height: 40px;
            font-weight: bold;
            color: #fff;
            background-color: #0F4CAF;
        }

        .timeline-step .status-badge {
            margin-top: 5px;
        }

        .timeline-step .timestamp {
            font-size: 12px;
            color: #888;
        }

        .timeline-step .label {
            font-size: 14px;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .timeline-step .approver {
            font-size: 13px;
            color: #555;
        }
    </style>
@endsection
