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

                        <form id="searchForm" method="GET" action="{{ route('HR.reporthrover') }}">

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
                                            <a href="{{ route('HR.reporthrover') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการเบิกเกิน 7 วัน</h5>
                    <div class="d-flex justify-content-end mb-3 pe-3"> {{-- เพิ่ม padding-end --}}
                        <button type="button" id="btnExport" class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> EXPORT
                        </button>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table" id="listexpense">
                            <thead class="table-dark">
                                <tr>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <th>NO.</th>
                                <th>BOOKING ID</th>
                                <th>DATETIME BOOK</th>
                                <th>EMPID</th>
                                <th>FULLNAME</th>
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
