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

                        <form id="searchForm">
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <div class="row">
                                        <label class="col-sm-3 col-form-label text-sm-end" for="formtabs-first-name">รายชื่อ
                                            พขร.</label>
                                        <div class="col-sm-9">
                                            <select name="drivers" id="drivers" class="form-select form-select-l">
                                                <option value="">เลือกชื่อ</option>
                                                @foreach ($drivers as $driver)
                                                    <option value="{{ $driver->empid }}">
                                                        {{ $driver->empid . ' ' . $driver->fullname }}</option>
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
                                                    class="mdi mdi-file-search-outline"></span>ค้นหา</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div id="resultArea">
                <!-- จะโหลดรายการเบิกรถมาแสดงตรงนี้ -->

            </div>

        </div>
    </div>
@endsection
@section('csscustom')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
@endsection
@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
@endsection
@section('jscustom')
    <script>
        $(document).ready(function() {
            $('#drivers').select2();
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();

                const empid = $('#drivers').val();
                if (!empid) return;

                $.get('/DriverClaim/search-booking', {
                    empid
                }, function(res) {
                    $('#resultArea').html(res); // แสดงผล HTML ที่ได้จาก controller
                }).fail(function() {
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                });
            });

        });
    </script>
@endsection
