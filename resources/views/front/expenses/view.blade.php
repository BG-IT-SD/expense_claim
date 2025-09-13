@extends('layouts.template')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- <h4 class="py-3 mb-4 bg-secondary"><span class="text-muted fw-light ">Form/</span> เบิกเบี้ยงเลี้ยง/อาหาร/ค่าเดินทาง</h4> --}}
        <div class="card shadow-none bg-transparent border border-secondary">
            <div class="card-body text-secondary">
                <h4 class="card-title text-secondary">เบิกเบี้ยงเลี้ยง/อาหาร/ค่าเดินทาง</h4>
            </div>
        </div>
        <!-- Default -->
        <div class="row">
            <!-- <div class="col-12">
                                    <h5>Default</h5>
                                </div> -->

            <!-- Default Wizard -->
            <div class="col-12 mb-4">
                <!-- <small class="text-light fw-medium">Basic</small> -->
                <div class="bs-stepper wizard-numbered mt-2">
                    <div class="bs-stepper-header">
                        <div class="step crossed" data-target="#account-details">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-number">01</span>
                                    <span class="d-flex flex-column gap-1 ms-2">
                                        <span class="bs-stepper-title">การขออนุมัติการไปปฏิบัติงานนอกสถานที่</span>
                                        <span class="bs-stepper-subtitle">Booking ID :</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step crossed" data-target="#personal-info">
                            <button type="button" class="step-trigger" aria-selected="false">
                                <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-number">02</span>
                                    <span class="d-flex flex-column gap-1 ms-2">
                                        <span class="bs-stepper-title">การขออนุมัติเบิกค่าเบี้ยเลี้ยง</span>
                                        <span class="bs-stepper-subtitle">ค่าอาหาร</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step active" data-target="#social-links">
                            <button type="button" class="step-trigger" aria-selected="true">
                                <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-number">03</span>
                                    <span class="d-flex flex-column gap-1 ms-2">
                                        <span class="bs-stepper-title">การขออนุมัติเบิกค่าเบี้ยเลี้ยง</span>
                                        <span class="bs-stepper-subtitle">ค่าเดินทาง</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <form action="#" id="expensefrmview" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- hidden --}}
                            <input type="hidden" name="page_mode" id="page_mode" value="{{ $isView ?? 99 }}">
                            {{-- <input type="hidden" name="extype" value="{{ $typegroup }}">
                            <input type="hidden" name="bookid" value="{{ $booking->id }}">
                            <input type="hidden" name="empid" value="{{ $empid }}">
                            <input type="hidden" name="locationbu" value="{{ $booking->locationbu }}">
                            <input type="hidden" name="locationid" value="{{ $booking->locationid }}"> --}}
                            {{-- hidden --}}
                            <!-- Tab1 Details -->
                            @include('front.expenses.views.expenset1')
                            <!-- Tab2 Info -->
                            @include('front.expenses.views.expenset2')
                            <!-- Tab3 Links -->
                            @include('front.expenses.views.expenset3')
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Default Wizard -->


        </div>
        <hr class="container-m-nx mb-5">

    </div>
    </div>
    {{-- @include('front.expenses.modal') --}}
@endsection
@section('csscustom')
    <style>
        #map {
            height: 500px;
        }


        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #origin-input,
        #destination-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 200px;
        }

        #origin-input:focus,
        #destination-input:focus {
            border-color: #4d90fe;
        }

        #mode-selector {
            color: #fff;
            background-color: #4d90fe;
            margin-left: 12px;
            padding: 5px 11px 0px 11px;
        }

        #mode-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

        /* timeline approve */
        .timeline-horizontal {
            position: relative;
            display: flex;
            justify-content: flex-start; /* 👈 เปลี่ยนจาก space-between เป็น flex-start */
            align-items: flex-start;
            flex-wrap: nowrap;
            gap: 50px; /* เพิ่มระยะห่างระหว่างจุด */
            padding: 30px 10px;
        }

        .timeline-horizontal::before {
            content: "";
            position: absolute;
            top: 40px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #ccc;
            z-index: 0;
        }

        .timeline-step {
            position: relative;
            text-align: center;
            z-index: 1;
            flex: 1;
            min-width: 140px;
        }

        .timeline-step .circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0 auto 8px;
            line-height: 40px;
            font-weight: bold;
            color: #fff;
            background-color: #0F4CAF;
        }

        .timeline-step .status-badge {
            margin-top: 5px;
        }

        .timeline-step .timestamp {
            font-size: 12px;
            color: #888;
        }

        .timeline-step .label {
            font-size: 14px;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .timeline-step .approver {
            font-size: 13px;
            color: #555;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/dropzone/dropzone.css') }}" />
@endsection


@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
@endsection

@section('jscustom')
    <script src="{{ asset('template/assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/pickr/pickr.js') }}"></script>

    {{-- <script src="{{ asset('template/assets/js/form-wizard-numbered.js') }}"></script> --}}
    <script src="{{ asset('template/assets/js/form-wizard-validation.js') }}"></script>

    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/expense.js']) }}"></script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/multi-step-view.js']) }}"></script>

@endsection
