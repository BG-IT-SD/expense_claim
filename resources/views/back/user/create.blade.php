@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Role /</span><span> Add Data</span></h4>
        {{-- {{ isset($fuelPrice) ? 'Edit':'Add' }} --}}
        <div class="app-ecommerce">
            <!-- Add Product -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1 mt-3">เพิ่มข้อมูล</h4>
                    {{-- <p></p> --}}
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('User') }}';">Discard</button>
                    {{-- <button class="btn btn-outline-primary">Save draft</button> --}}

                </div>

            </div>

            <div class="row">
                <!-- Main form-->
                <div class="col-12 col-lg-12">
                    <!-- Product Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            {{-- <h5 class="card-tile mb-0">From New Data</h5> --}}
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="row" id="content-check">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="empid" name="empid">
                                            <label for="empid">Employee ID</label>
                                        </div>
                                        <div id="empid-error" class="text-danger small"></div>

                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="idcard" name="idcard">
                                            <label for="idcard">เลขบัตรประจำตัวประชาชน</label>
                                        </div>
                                        <div id="idcard-error" class="text-danger small"></div>

                                    </div>
                                    <div class="mt-3 text-center">
                                        <button type="button" id="checkempid" class="btn btn-primary"><span
                                                class="mdi mdi-check-circle"></span> ตรวจสอบข้อมูล</button>
                                    </div>
                                </div>
                                <div class="row" id="content-register">
                                    <div class="col-md-12 mb-3">
                                        <ul class="list-unstyled my-3 py-1">
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-identifier mdi-24px"></i><span
                                                    class="fw-medium mx-2">รหัสพนักงาน:</span>
                                                <span id="text-empid"></span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-account-outline mdi-24px"></i><span
                                                    class="fw-medium mx-2">ชื่อ - นามสกุล:</span> <span id="text-fullname"></span>
                                            </li>

                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-star-outline mdi-24px"></i><span
                                                    class="fw-medium mx-2">แผนก:</span>
                                                <span id="text-dept"></span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="mdi mdi-email-check-outline mdi-24px"></i><span
                                                    class="fw-medium mx-2">E-Mail:</span>
                                                <span  id="text-email"></span>
                                            </li>
                                            <li class="d-flex align-items-center mb-1">
                                                <i class="mdi mdi-domain mdi-24px"></i><span
                                                    class="fw-medium mx-2">Bu:</span>
                                                <span  id="text-bu"></span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-sm-12 form-password-toggle mb-3">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="password" id="password" name="password"
                                                    class="form-control"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="password" />
                                                <label for="password">Password</label>
                                            </div>
                                            <span class="input-group-text cursor-pointer" id="password"><i
                                                    class="mdi mdi-eye-off-outline"></i></span>
                                        </div>
                                        <div id="password-error" class="text-danger small"></div>
                                    </div>

                                    <div class="col-sm-12 form-password-toggle mb-3">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="password" id="password_confirmation" name="password_confirmation"
                                                    class="form-control"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="password_confirmation" />
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input type="hidden" name="checkEmpid" id="checkEmpid"
                                                    class="form-control" />
                                            </div>
                                            <span class="input-group-text cursor-pointer" id="password_confirmation"><i
                                                    class="mdi mdi-eye-off-outline"></i></span>
                                        </div>
                                        <div id="repassword-error" class="text-danger small"></div>
                                    </div>
                                    <hr>
                                    <button type="button" id="saveButton" class="btn btn-primary"><span
                                        class="mdi mdi-content-save"></span>
                                    &nbsp;Save</button>

                                </div>
                            </form>

                        </div>
                    </div>
                    <!-- /Product Information -->
                </div>
                <!-- End Main form-->
            </div>
        </div>
    </div>
@endsection
@section('jscustom')
    <script>
        const CheckEmpURL = "{{ route('CheckEmpID') }}";
        const RegisterURL = "{{ route('register') }}";
    </script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/setting/usercreate.js']) }}"></script>
@endsection
