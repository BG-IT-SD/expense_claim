@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Basic Layout -->
            <div class="colxxl">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-6">
                            <h5><span class="mdi mdi-account-plus-outline"></span> เพิ่มรายชื่อ</h5>
                        </div>
                        <div class="col-md-6 text-end">

                        </div>

                    </div>

                    <div class="card-body pt-0">
                        <form action="{{ route('HRgroup.savelisthr') }}" name="listhrgroup" method="POST">
                            @csrf
                            <div class="row" id="content-check">
                                <div class="col-md-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="empid" name="empid">
                                        <label for="empid">Employee ID</label>
                                    </div>
                                    <div id="empid-error" class="text-danger small"></div>

                                </div>
                                <div class="mt-3 text-center">
                                    <button type="button" id="checkempid" class="btn btn-primary"><span
                                            class="mdi mdi-check-circle"></span> ตรวจสอบข้อมูล</button>
                                            <a href="{{ route('HRgroup.edit',$id) }}" class="btn btn-danger">กลับ</a>
                                </div>
                            </div>

                            <div class="row" id="content-register">

                                <div class="col-md-12 mb-3">
                                    <ul class="list-unstyled my-3 py-1">
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-identifier mdi-24px"></i><span
                                                class="fw-medium mx-2">รหัสพนักงาน:</span>
                                            <span id="text-empid"></span>
                                            <input type="hidden" name="groupid" value="{{ $id }}">
                                            <input type="hidden" id="emp_data" name="emp_data">
                                            <input type="hidden" id="email_data" name="email_data">
                                            <input type="hidden" id="name_data" name="name_data">
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-account-outline mdi-24px"></i><span
                                                class="fw-medium mx-2">ชื่อ - นามสกุล:</span> <span
                                                id="text-fullname"></span>
                                        </li>

                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-star-outline mdi-24px"></i><span
                                                class="fw-medium mx-2">แผนก:</span>
                                            <span id="text-dept"></span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-email-check-outline mdi-24px"></i><span
                                                class="fw-medium mx-2">E-Mail:</span>
                                            <span id="text-email"></span>
                                        </li>
                                        <li class="d-flex align-items-center mb-1">
                                            <i class="mdi mdi-domain mdi-24px"></i><span class="fw-medium mx-2">Bu:</span>
                                            <span id="text-bu"></span>
                                        </li>
                                    </ul>
                                    <hr>
                                    <div class="mt-3 text-center">
                                        <button type="submit"  class="btn btn-primary"><span
                                                class="mdi mdi-content-save"></span> บันทึกข้อมูล</button>
                                                <a href="{{ route('HRgroup.edit',$id) }}" class="btn btn-danger">กลับ</a>
                                    </div>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
<script>
    const CheckEmpURL = "{{ route('HRgroup.checkemphr') }}";
</script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/hrgroup.js']) }}"></script>
@endsection
