@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">

                <div class="card p-3">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิกที่ตรวจสอบแล้ว</h5>
                    <div class="row">
                        <div class="col-md-6 mt-3 mb-3">
                            <input type="hidden" id="selectedPlantName" name="plant_name" value="">
                            <input type="hidden" id="plantID" name="plant_id" value="">
                        </div>
                        <div class="col-md-6 mt-3 mb-3 text-end"> <button type="button" class="btn btn-primary"
                                id="sendSelected">
                                <i class="mdi mdi-content-save"></i> ยืนยันข้อมูลเพื่ออนุมัติ
                            </button></div>
                    </div>

                    {{-- Newtabs --}}
                    <div class="card mb-4">
                        <div class="card-header p-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    {{-- Head Tabs active --}}
                                    @foreach ($plantNames as $plant)
                                        <li class="nav-item" role="presentation">
                                            <button type="button"
                                                class="nav-link  {{ $loop->first ? 'active' : '' }} waves-effect"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#plant_{{ $plant['id'] }}"
                                                aria-controls="{{ $plant['id'] }}" data-plantname="{{ $plant['name'] }}"
                                                data-plantid="{{ $plant['id'] }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                {{ $plant['name'] }}
                                            </button>
                                        </li>
                                    @endforeach

                                    {{-- Head Tabs --}}
                                    <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content p-0">
                                {{-- content Tabs show active --}}
                                @foreach ($plantNames as $plant)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="plant_{{ $plant['id'] }}" role="tabpanel">

                                        <div class="table-responsive text-nowrap2">
                                            <table class="table appex2 table-sticky" id="appex-{{ $plant['id'] }}">
                                                <thead class="table-dark">
                                                    <tr>
                                                        {{-- checkbox รวมทั้ง plant --}}
                                                        <th class="sticky-col col-check">
                                                            <input type="checkbox" class="selectAll"
                                                                data-plant="plant_{{ $plant['id'] }}" />
                                                        </th>

                                                        {{-- คอลัมน์ที่ต้องการให้ติดซ้าย --}}
                                                        <th class="sticky-col col-expense">EXPENSE ID</th>
                                                        <th class="sticky-col col-datetime">DATE TIME</th>
                                                        <th class="sticky-col col-booking">BOOKING ID</th>
                                                        <th class="sticky-col col-emp">ID | NAME</th>

                                                        {{-- คอลัมน์ที่เหลือ --}}
                                                        <th>BU</th>
                                                        <th>LOCATION</th>
                                                        <th>จำนวนวัน</th>
                                                        <th>1. ค่าเบี้ยเลี้ยง / อาหาร</th>
                                                        <th>2. ค่าน้ำมัน</th>
                                                        <th>3. ค่าทางด่วน</th>
                                                        <th>4. ค่ารถโดยสารสาธารณะ</th>
                                                        <th>5. ค่าใช้จ่ายอื่นๆ</th>
                                                        <th>Total</th>
                                                        <th>Type Approve</th>
                                                        <th>Approve</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="table-border-bottom-0">
                                                    @php
                                                        // ดึงรายการเฉพาะของโรงงานนี้ ถ้าไม่มีให้เป็น collection ว่าง
                                                        $plantExpenses = $expensesByPlant[$plant['name']] ?? collect();
                                                    @endphp

                                                    @foreach ($plantExpenses as $expense)
                                                        @php
                                                            // คำนวณจำนวนวัน
                                                            if ($expense->extype == 2) {
                                                                $days =
                                                                    \Carbon\Carbon::parse(
                                                                        $expense->vbookingdrv->departure_date,
                                                                    )->diffInDays(
                                                                        \Carbon\Carbon::parse(
                                                                            $expense->vbookingdrv->return_date,
                                                                        ),
                                                                    ) + 1;
                                                            } else {
                                                                $days =
                                                                    \Carbon\Carbon::parse(
                                                                        $expense->vbooking->departure_date,
                                                                    )->diffInDays(
                                                                        \Carbon\Carbon::parse(
                                                                            $expense->vbooking->return_date,
                                                                        ),
                                                                    ) + 1;
                                                            }

                                                            // ยอดค่าใช้จ่ายแต่ละประเภท
                                                            $food = $expense->costoffood ?? 0;
                                                            $gas = $expense->gasolinecost ?? 0;
                                                            $express = $expense->expresswaytoll ?? 0;
                                                            $publictransport = $expense->publictransportfare ?? 0;
                                                            $other = $expense->otherexpenses ?? 0;
                                                            $total =
                                                                $food + $gas + $express + $publictransport + $other;
                                                        @endphp

                                                        <tr>
                                                            {{-- checkbox แถว --}}
                                                            <td class="sticky-col col-check">
                                                                <input type="checkbox" name="expense_ids[]"
                                                                    class="expense-checkbox"
                                                                    data-plant="plant_{{ $plant['id'] }}"
                                                                    value="{{ $expense->id }}">
                                                            </td>

                                                            {{-- ===== 4 คอลัมน์ที่ sticky ===== --}}
                                                            <td class="sticky-col col-expense">
                                                                {{ $expense->prefix . $expense->id }}
                                                            </td>

                                                            <td class="sticky-col col-datetime text-wrap">
                                                                @if ($expense->extype == 2)
                                                                    @if ($expense->vbookingdrv)
                                                                        {{ \Carbon\Carbon::parse($expense->vbookingdrv->departure_date . ' ' . $expense->vbookingdrv->departure_time)->format('d/m/Y H:i') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($expense->vbookingdrv->return_date . ' ' . $expense->vbookingdrv->return_time)->format('d/m/Y H:i') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                @else
                                                                    @if ($expense->vbooking)
                                                                        {{ \Carbon\Carbon::parse($expense->vbooking->departure_date . ' ' . $expense->vbooking->departure_time)->format('d/m/Y H:i') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($expense->vbooking->return_date . ' ' . $expense->vbooking->return_time)->format('d/m/Y H:i') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                @endif
                                                            </td>

                                                            <td class="sticky-col col-booking">
                                                                {{ $expense->bookid }}
                                                            </td>

                                                            <td class="sticky-col col-emp text-wrap">
                                                                @if ($expense->extype == 2 || $expense->extype == 3)
                                                                    {{ $expense->empid . ' | ' . ($expense->tech->fullname ?? '-') }}
                                                                @else
                                                                    {{ $expense->empid . ' | ' . ($expense->user->fullname ?? '-') }}
                                                                @endif
                                                            </td>
                                                            {{-- ===== /sticky cols ===== --}}

                                                            <td class="text-wrap">
                                                                {{ BuEmp($expense->empid) }}
                                                            </td>

                                                            <td>
                                                                @if ($expense->extype == 2)
                                                                    {{ $expense->vbookingdrv?->locationbu ?? '-' }}
                                                                @else
                                                                    {{ $expense->vbooking?->locationbu ?? '-' }}
                                                                @endif
                                                            </td>

                                                            <td>{{ $days }}</td>
                                                            <td>{{ number_format($food, 2) }}</td>
                                                            <td>{{ number_format($gas, 2) }}</td>
                                                            <td>{{ number_format($express, 2) }}</td>
                                                            <td>{{ number_format($publictransport, 2) }}</td>
                                                            <td>{{ number_format($other, 2) }}</td>
                                                            <td>{{ number_format($total, 2) }}</td>

                                                            <td>
                                                                @if (!is_null($expense->latestApprove->typeapprove ?? null))
                                                                    {!! type_approve_text($expense->latestApprove->typeapprove, $expense->latestApprove->typeapprove) !!}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                @if (!is_null($expense->latestApprove->statusapprove ?? null))
                                                                    {!! hr_status_approve_badge($expense->latestApprove->statusapprove, $expense->latestApprove->typeapprove) !!}
                                                                @endif
                                                            </td>

                                                            <td class="text-nowrap">
                                                                @if (($expense->latestApprove->statusapprove ?? null) == 2)
                                                                    {{-- reject แล้ว ดูได้อย่างเดียว --}}
                                                                    @if ($expense->extype == 2)
                                                                        <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                                            target="_blank" class="btn btn-sm btn-info">
                                                                            <span
                                                                                class="mdi mdi-eye-arrow-right-outline"></span>
                                                                            View
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                                            target="_blank" class="btn btn-sm btn-info">
                                                                            <span
                                                                                class="mdi mdi-eye-arrow-right-outline"></span>
                                                                            View
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    @if ($expense->extype == 2)
                                                                        <a href="{{ route($page, ['id' => $expense->id, 'type' => 0]) }}"
                                                                            target="_blank" class="btn btn-sm btn-info">
                                                                            <span
                                                                                class="mdi mdi-eye-arrow-right-outline"></span>
                                                                            View
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ route('HR.view', ['id' => $expense->id, 'type' => '0']) }}"
                                                                            target="_blank" class="btn btn-sm btn-info">
                                                                            <span
                                                                                class="mdi mdi-eye-arrow-right-outline"></span>
                                                                            View
                                                                        </a>
                                                                        <a href="{{ route('HR.aftedit', $expense->id) }}"
                                                                            class="btn btn-sm btn-warning">
                                                                            <span class="mdi mdi-edit"></span> Edit
                                                                        </a>
                                                                    @endif
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger btn-open-reject"
                                                                        data-expense-id="{{ $expense->id }}"
                                                                        data-empemail="{{ $expense->extype == 2 || $expense->extype == 3 ? EmailEmp($expense->empid) : $expense->user->email }}"
                                                                        data-empfullname="{{ $expense->extype == 2 || $expense->extype == 3 ? $expense->tech->fullname ?? '' : $expense->user->fullname ?? '' }}"
                                                                        data-departuredaterj="@if ($expense->extype == 2 && $expense->vbookingdrv) {{ \Carbon\Carbon::parse($expense->vbookingdrv->departure_date)->format('d/m/Y') }}
                                                                                @elseif($expense->vbooking)
                                                                                    {{ \Carbon\Carbon::parse($expense->vbooking->departure_date)->format('d/m/Y') }} @endif">
                                                                        REJECT
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                @endforeach

                                {{-- content Tabs --}}

                            </div>
                        </div>
                    </div>
                    {{-- Newtabs --}}


                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="popUpReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ระบุเหตุผลการไม่อนุมัติ (Reject)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="rejectfrm">
                        @csrf
                        <div class="mb-3">
                            <label for="rejectremark" class="form-label">เหตุผลที่ไม่อนุมัติ</label>
                            <textarea name="rejectremark" id="rejectremark" class="form-control" rows="4"
                                placeholder="กรอกเหตุผลที่ไม่อนุมัติ..."></textarea>
                        </div>

                        {{-- hidden field --}}

                        <input type="hidden" id="rejectidexpense" name="rejectidexpense">

                        {{-- ข้อมูลหัวหน้า (คนกด reject = HR) --}}
                        <input type="hidden" id="head_emailrj" name="head_emailrj" value="{{ Auth::user()->email }}">
                        <input type="hidden" id="head_namerj" name="head_namerj"
                            value="{{ Auth::user()->fullname ?? Auth::user()->name }}">
                        <input type="hidden" id="head_idrj" name="head_idrj" value="{{ Auth::user()->empid }}">

                        {{-- ข้อมูลพนักงานผู้ขอเบิก --}}
                        <input type="hidden" id="departuredaterj" name="departuredaterj">
                        <input type="hidden" id="empemailrj" name="empemailrj">
                        <input type="hidden" id="empfullname" name="empfullname">
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-danger btnreject">ยืนยัน Reject</button>
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
        const hrNextApproveUrl = "{{ route('HR.hrnextapprove') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/approve.js']) }}"></script>
@endsection

@section('csscustom')
    <style>
        /* 1. ตัดช่องว่างระหว่างเซลล์ของตารางนี้ให้หมด */
        table.table-sticky {
            border-collapse: collapse !important;
            border-spacing: 0 !important;
        }

        /* 2. sticky column พื้นฐาน */
        .table-sticky .sticky-col {
            position: sticky;
            z-index: 5;
        }

        .table-sticky thead .sticky-col {
            z-index: 6;
            background-color: #343a40 !important;
            /* สีเดียวกับ table-dark */
            color: #fff !important;
        }

        .table-sticky tbody .sticky-col {
            background-color: #fff;
        }

        /* 3. กำหนดระยะ left + width ให้คอลัมน์ที่ติดซ้ายชนกันสนิท */
        .col-check {
            left: 0;
            width: 50px;
            min-width: 50px;
            max-width: 50px;
        }

        .col-expense {
            left: 50px;
            /* = 50 (check) */
            width: 160px;
            min-width: 160px;
            max-width: 160px;
        }

        .col-datetime {
            left: 210px;
            /* = 50 + 160 */
            width: 200px;
            min-width: 200px;
            max-width: 200px;
        }

        .col-booking {
            left: 410px;
            /* = 210 + 200 */
            width: 120px;
            min-width: 120px;
            max-width: 120px;
        }

        .col-emp {
            left: 530px;
            /* = 410 + 120 */
            width: 260px;
            min-width: 260px;
            max-width: 260px;
        }
    </style>
@endsection
