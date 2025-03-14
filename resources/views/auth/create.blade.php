@extends('layouts.login.templatelogin')
@section('contentlogin')
    <!--  Multi Steps Registration -->
    <div class="d-flex col-lg-9 align-items-center justify-content-center p-5 ">
        <div class="w-px-700 mt-5 mt-lg-0">
            <div id="multiStepsValidation" class="bs-stepper wizard-numbered shadow-none">
                <div class="bs-stepper-header border-bottom-0">
                    <div class="step" data-target="#accountDetailsValidation">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-number">01</span>
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Check</span>
                                    <span class="bs-stepper-subtitle">ตรวจสอบข้อมูล</span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#personalInfoValidation">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                            <span class="bs-stepper-label">
                                <span class="bs-stepper-number">02</span>
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Password</span>
                                    <span class="bs-stepper-subtitle">สร้างรหัสผ่าน</span>
                                </span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content">
                    <form id="multiStepsForm" onSubmit="return false">
                        <!-- Account Details -->
                        <div id="accountDetailsValidation" class="content">
                            <div class="content-header mb-3">
                                <h4 class="mb-0">Check Employee ID</h4>
                                <small>ตรวจสอบข้อมูลพนักงาน</small>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="multiStepsEmpid" id="multiStepsEmpid"
                                            class="form-control" />
                                        <label for="multiStepsEmpid">Employee ID</label>
                                    </div>
                                    <div id="empid-error" class="text-danger small"></div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="multiStepsIDCard" id="multiStepsIDCard"
                                            class="form-control" />
                                        <label for="multiStepsIDCard">เลขที่บัตรประชาชน</label>
                                    </div>
                                </div>



                                <div class="col-12 d-flex justify-content-between">
                                    <button class="btn btn-secondary btn-prev" disabled>
                                        <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>
                                    <button class="btn btn-primary btn-next">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span>
                                        <i class="mdi mdi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Personal Info -->
                        <div id="personalInfoValidation" class="content">
                            <div class="content-header mb-3">
                                <h4 class="mb-0">Create Password</h4>
                                <small>สร้างรหัสผ่าน</small>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-12 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="multiStepsPass" name="multiStepsPass"
                                                class="form-control"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-describedby="multiStepsPass2" />
                                            <label for="multiStepsPass">Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer" id="multiStepsPass2"><i
                                                class="mdi mdi-eye-off-outline"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-password-toggle">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="multiStepsConfirmPass" name="multiStepsConfirmPass"
                                                class="form-control"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-describedby="multiStepsConfirmPass2" />
                                            <label for="multiStepsConfirmPass">Confirm Password</label>
                                            <input type="hidden" name="checkEmpid" id="checkEmpid"
                                                class="form-control" />
                                        </div>
                                        <span class="input-group-text cursor-pointer" id="multiStepsConfirmPass2"><i
                                                class="mdi mdi-eye-off-outline"></i></span>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                    <button class="btn btn-secondary btn-prev">
                                        <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-next btn-submit">Submit</button>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Multi Steps Registration -->
@endsection


@section('jslogin')
    <!-- Vendor -->
    <script src="{{ asset('template/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
    {{-- <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script> --}}
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <!-- Page -->
    <script src="{{ asset('template/assets/js/pages-auth-multisteps.js') }}"></script>
@endsection


@section('csslogin')
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('template//assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection
