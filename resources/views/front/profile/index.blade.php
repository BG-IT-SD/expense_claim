@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">User /</span><span> Profile</span></h4>
        {{-- {{ isset($fuelPrice) ? 'Edit':'Add' }} --}}
        <div class="app-ecommerce">
            <!-- Add Product -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div class="d-flex flex-column justify-content-center">
                    {{-- <h4 class="mb-1 mt-3">Profile</h4> --}}
                    {{-- <p></p> --}}
                </div>
                <div class="d-flex align-content-center flex-wrap gap-3">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='{{ url('Expense') }}';">Discard</button>
                    <button type="button" id="resetpassword" class="btn btn-danger"  onclick="window.location.href='{{ route('profile.reset') }}';"> Reset
                        Password</button>

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
                            <div class="row" id="content-edregister">
                                <div class="col-md-12 mb-3">
                                    <ul class="list-unstyled my-3 py-1">
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-identifier mdi-24px"></i><span
                                                class="fw-medium mx-2">รหัสพนักงาน:</span>
                                            <span id="text-empid">{{ $users->empid }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-account-outline mdi-24px"></i><span
                                                class="fw-medium mx-2">ชื่อ - นามสกุล:</span> <span
                                                id="text-fullname">{{ $users->fullname }}</span>
                                        </li>

                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-star-outline mdi-24px"></i><span
                                                class="fw-medium mx-2">แผนก:</span>
                                            <span id="text-dept">{{ $users->dept }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="mdi mdi-email-check-outline mdi-24px"></i><span
                                                class="fw-medium mx-2">E-Mail:</span>
                                            <span id="text-email">{{ $users->email }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-1">
                                            <i class="mdi mdi-domain mdi-24px"></i><span class="fw-medium mx-2">Bu:</span>
                                            <span id="text-bu">{{ $users->bu }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-1">
                                            <i class="mdi mdi-cash-multiple mdi-24px"></i><span class="fw-medium mx-2">Bank
                                                Account:</span>
                                            <span id="text-account">
                                                <button type="button" class="btn btn-sm btn-warning text-nowrap"
                                                    data-bs-animation="true" data-bs-toggle="popover"
                                                    data-bs-placement="right" data-bs-content="{{ $account->NUMBANK }}"
                                                    title="Account Number">
                                                    <span class="mdi mdi-eye-lock"></span>
                                                </button>

                                        </li>


                                    </ul>
                                </div>
                            </div>
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
    <script src="{{ asset('template/assets/js/ui-popover.js') }}"></script>
@endsection
