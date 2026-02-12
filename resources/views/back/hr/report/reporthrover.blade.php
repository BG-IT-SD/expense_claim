@extends('layouts.template')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Search --}}
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><span class="mdi mdi-file-search-outline"></span> ค้นหาข้อมูล</h5>
                    </div>
                    <div class="card-body">
                        <form id="searchForm" method="GET" action="{{ route('HR.reporthrover') }}">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="exdate">Start Date</label>
                                        <div class="col-sm-9">
                                            {{-- start = end - 30 --}}
                                            <input type="text" id="exdate" name="exdate"
                                                value="{{ request('exdate', now()->subDays(7)->subDays(30)->format('Y-m-d')) }}"
                                                class="form-control" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="end_exdate">End Date</label>
                                        <div class="col-sm-9">
                                            {{-- end = วันนี้ - 7 --}}
                                            <input type="text" id="end_exdate" name="end_exdate"
                                                value="{{ request('end_exdate', now()->subDays(7)->format('Y-m-d')) }}"
                                                class="form-control" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="status">Status</label>
                                        <div class="col-sm-9">
                                            <select name="status" id="status" class="form-select">
                                                <option value="">-- เลือกสถานะ --</option>
                                                @foreach ($statusList as $key => $text)
                                                    <option value="{{ $key }}" @selected((string) request('status') === (string) $key)>
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
                                            <button type="submit" class="btn btn-primary me-sm-3 me-1">
                                                <span class="mdi mdi-file-search-outline"></span>
                                            </button>
                                            <a href="{{ route('HR.reporthrover') }}"
                                                class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการ booking ที่ยังเบิกไม่ครบ</h5>

                    <div class="d-flex justify-content-end mb-3 pe-3">
                        <button type="button" id="btnExport" class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> EXPORT
                        </button>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table" id="listexpense">
                            <thead class="table-dark">
                                <tr>
                                    <th>NO.</th>
                                    <th>BOOKING ID</th>
                                    <th>RETURN_DATE</th>
                                    <th>EMPID (ที่ยังไม่เบิก)</th>
                                    <th>ROLE</th>
                                    <th>FULLNAME</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@section('jscustom')
<script>
    const HR_REPORT_DATA_URL   = "{{ route('HR.reporthr.overdata') }}";
    const HR_REPORT_EXPORT_URL = "{{ route('HR.reporthr.overexport') }}";
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---------- helpers ----------
    function formatYMD(dateObj) {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function parseYMD(str) {
        // str: 'YYYY-MM-DD'
        const parts = (str || '').split('-');
        if (parts.length !== 3) return null;
        const y = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10);
        const d = parseInt(parts[2], 10);
        if (!y || !m || !d) return null;
        return new Date(y, m - 1, d);
    }

    // ---------- business dates ----------
    const today = new Date();
    const endLimit = new Date(today);
    endLimit.setDate(endLimit.getDate() - 7); // ✅ today-7 (เส้นตาย)

    // ถ้า input ว่าง ให้เซ็ตค่าเริ่มต้นเอง (กันเคสไม่ได้ใส่ default ที่ blade)
    const endInput = document.getElementById('end_exdate');
    const startInput = document.getElementById('exdate');

    if (!endInput.value) {
        endInput.value = formatYMD(endLimit);
    }

    if (!startInput.value) {
        const startDefault = new Date(endLimit);
        startDefault.setDate(startDefault.getDate() - 30); // ✅ end-30
        startInput.value = formatYMD(startDefault);
    }

    // ---------- flatpickr ----------
    const startPicker = flatpickr("#exdate", {
        dateFormat: "Y-m-d",
        monthSelectorType: "static",
    });

    const endPicker = flatpickr("#end_exdate", {
        dateFormat: "Y-m-d",
        maxDate: endLimit,
        monthSelectorType: "static",
        onChange: function () {
            syncStartRange();
        }
    });

    function syncStartRange() {
        // endDate = ค่าในช่อง หรือ endLimit
        const endVal = endInput.value;
        const endDate = parseYMD(endVal) || endLimit;

        // minStart = end-30
        const minStart = new Date(endDate);
        minStart.setDate(minStart.getDate() - 30);

        // lock range start
        startPicker.set('minDate', minStart);
        startPicker.set('maxDate', endDate);

        // ถ้า start ปัจจุบันอยู่นอกช่วง ให้ดึงกลับเข้าช่วง
        const curStart = parseYMD(startInput.value);
        if (!curStart || curStart < minStart) {
            startPicker.setDate(minStart, true); // true = triggerChange
        } else if (curStart > endDate) {
            startPicker.setDate(endDate, true);
        }
    }

    // ตั้งช่วงครั้งแรก
    syncStartRange();

    // ---------- DataTable ----------
    const table = $('#listexpense').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: HR_REPORT_DATA_URL,
            data: function (d) {
                d.exdate     = $('#exdate').val();
                d.end_exdate = $('#end_exdate').val();
                d.status     = $('#status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'booking_id', name: 'booking_id' },
            { data: 'datetime_book', name: 'datetime_book' },
            { data: 'empid', name: 'empid' },
            { data: 'role', name: 'role' },
            { data: 'fullname', name: 'fullname' },
        ],
        order: [[2, 'desc']]
    });

    // ✅ FIX: ใช้ jQuery .on แทน addEventListener บน jQuery object
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        syncStartRange();      // กัน start หลุดช่วงก่อนค้นหา
        table.ajax.reload();
    });

    // ---------- Export ----------
    document.getElementById('btnExport').addEventListener('click', function () {
        syncStartRange(); // กัน start หลุดช่วงก่อน export

        const params = new URLSearchParams({
            exdate: $('#exdate').val(),
            end_exdate: $('#end_exdate').val(),
            status: $('#status').val(),
        });

        window.location = HR_REPORT_EXPORT_URL + '?' + params.toString();
    });

});
</script>
@endsection

