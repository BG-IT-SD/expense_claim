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
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>สำเร็จ!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form method="GET" action="{{ route('HR.grouplist') }}">
                            <div class="row g-3">

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
                                <div class="col-md-6">

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
                                            <a href="{{ route('HR.grouplist') }}" class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการกลุ่มอนุมัติ</h5>
                    <div class="table-responsive text-nowrap2">
                        <table class="table" id="grouplist">
                            <thead class="table-dark">
                                <tr>
                                    <th>EXGROUPID</th>
                                    <th>BU</th>
                                    <th>DATE</th>
                                    <th>ยอดรวม</th>
                                    <th>ค่าอาหาร</th>
                                    <th>ค่าน้ำมัน</th>
                                    <th>ค่าใช้จ่ายอื่นๆ</th>
                                    <th>Type Approve</th>
                                    <th>Approve</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($exgroups as $exgroup)
                                    <tr>
                                        <td>{{ $exgroup->id }}</td>
                                        <td>{{ $exgroup->plantname }}</td>
                                        <td>{{ \Carbon\Carbon::parse($exgroup->groupdate)->format('d/m/Y') }}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotal, 2) : number_format($exgroup->total, 2) }}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotalfood, 2) : number_format($exgroup->totalfood, 2) }}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotalfuel, 2) : number_format($exgroup->totalfuel, 2)}}</td>
                                        <td>{{ isset($exgroup->nettotal)  ? number_format($exgroup->nettotalother, 2) : number_format($exgroup->totalother, 2) }}</td>
                                        <td>
                                            @if (!is_null($exgroup->typeapprove))
                                                {!! type_approve_text($exgroup->typeapprove, $exgroup->typeapprove) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if (!is_null($exgroup->statusapprove))
                                                {!! hr_status_approve_badge($exgroup->statusapprove, $exgroup->typeapprove) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($exgroup->typeapprove == 6 && $exgroup->statusapprove == 1)
                                                <a href="{{ route('HR.export.group.pdf', $exgroup->id) }}"
                                                    class="btn btn-sm btn-danger" target="_blank">
                                                    <span class="mdi mdi-file-pdf-box"></span>
                                                    {{-- PDF --}}
                                                </a>
                                                <a href="{{ route('HR.export.group.excel', $exgroup->id) }}"
                                                    class="btn btn-sm btn-success" target="_blank">
                                                    <span class="mdi mdi-file-excel"></span>
                                                    {{-- Excel --}}
                                                </a>
                                            @endif

                                            <a href="{{ route('HR.groupdetail', $exgroup->id) }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                <span class="mdi mdi-list-box"></span>
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/hr/groupapprove.js']) }}"></script>
@endsection
