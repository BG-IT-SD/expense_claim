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

                        <form id="searchForm" method="GET" action="{{ route('HR.reporthr') }}">

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
                                {{-- <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="bu">BU</label>
                                        <div class="col-sm-9">
                                            <select name="bu" id="bu" class="form-select">
                                                <option value="">-- เลือกBU --</option>
                                                @foreach ($plants as $plant)
                                                    <option value="{{ $plant->plantname }}" @selected(request('bu') == $plant->plantname)>
                                                        {{ $plant->plantname }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </div> --}}
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
                        <button type="button" id="btnExport" class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> EXPORT
                        </button>
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



                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
    <script>
        const HR_REPORT_DATA_URL = "{{ route('HR.reporthr.data') }}";
        const HR_REPORT_EXPORT_URL = "{{ route('HR.reporthr.export') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/reporthr.js']) }}"></script>
@endsection
