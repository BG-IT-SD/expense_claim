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
                                        <span class="bs-stepper-subtitle">Booking ID : {{ $booking->id }}</span>
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
                        <form onsubmit="return false">
                            {{-- hidden --}}
                            <input type="text" name="extype" value="{{ $typegroup }}">
                            <input type="text" name="bookid" value="{{ $booking->id }}">
                            <input type="text" name="empid" value="{{ Auth::user()->empid }}">
                            <input type="text" name="locationbu" value="{{ $booking->locationbu }}">
                            <input type="text" name="locationid" value="{{ $booking->locationid }}">
                            {{-- hidden --}}
                            <!-- Tab1 Details -->
                            @include('front.expenses.expenset1')
                            <!-- Tab2 Info -->
                            @include('front.expenses.expenset2')
                            <!-- Tab3 Links -->
                            @include('front.expenses.expenset3')
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Default Wizard -->


        </div>
        <hr class="container-m-nx mb-5">

    </div>
    </div>
    @include('front.expenses.modal')
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
    </style>
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('jscustom')
    <script src="{{ asset('template/assets/js/forms-file-upload.js') }}"></script>
    <script src="{{ asset('template/assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('template/assets/js/form-wizard-numbered.js') }}"></script>
    <script src="{{ asset('template/assets/js/form-wizard-validation.js') }}"></script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/expense.js']) }}"></script>
    <script></script>
    <script>
        // function initMap() {
        //     const map = new google.maps.Map(document.getElementById("map"), {
        //         mapTypeControl: false,
        //         center: {
        //             lat: 13.736717,
        //             lng: 100.523186
        //         }, // Default: Bangkok
        //         zoom: 13,
        //     });

        //     new AutocompleteDirectionsHandler(map);
        // }

        // class AutocompleteDirectionsHandler {
        //     map;
        //     originPlaceId;
        //     destinationPlaceId;
        //     travelMode;
        //     directionsService;
        //     directionsRenderer;
        //     constructor(map) {
        //         this.map = map;
        //         this.originPlaceId = "";
        //         this.destinationPlaceId = "";
        //         this.travelMode = google.maps.TravelMode.WALKING;
        //         this.directionsService = new google.maps.DirectionsService();
        //         this.directionsRenderer = new google.maps.DirectionsRenderer();
        //         this.directionsRenderer.setMap(map);

        //         const originInput = document.getElementById("origin-input");
        //         const destinationInput = document.getElementById("destination-input");
        //         const modeSelector = document.getElementById("mode-selector");
        //         // Specify just the place data fields that you need.
        //         const originAutocomplete = new google.maps.places.Autocomplete(
        //             originInput, {
        //                 fields: ["place_id"]
        //             },
        //         );
        //         // Specify just the place data fields that you need.
        //         const destinationAutocomplete = new google.maps.places.Autocomplete(
        //             destinationInput, {
        //                 fields: ["place_id"]
        //             },
        //         );

        //         this.setupClickListener(
        //             "changemode-walking",
        //             google.maps.TravelMode.WALKING,
        //         );
        //         this.setupClickListener(
        //             "changemode-transit",
        //             google.maps.TravelMode.TRANSIT,
        //         );
        //         this.setupClickListener(
        //             "changemode-driving",
        //             google.maps.TravelMode.DRIVING,
        //         );
        //         this.setupPlaceChangedListener(originAutocomplete, "ORIG");
        //         this.setupPlaceChangedListener(destinationAutocomplete, "DEST");
        //         this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
        //         this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(
        //             destinationInput,
        //         );
        //         this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
        //     }
        //     // Sets a listener on a radio button to change the filter type on Places
        //     // Autocomplete.
        //     setupClickListener(id, mode) {
        //         const radioButton = document.getElementById(id);

        //         radioButton.addEventListener("click", () => {
        //             this.travelMode = mode;
        //             this.route();
        //         });
        //     }
        //     setupPlaceChangedListener(autocomplete, mode) {
        //         autocomplete.bindTo("bounds", this.map);
        //         autocomplete.addListener("place_changed", () => {
        //             const place = autocomplete.getPlace();

        //             if (!place.place_id) {
        //                 window.alert("Please select an option from the dropdown list.");
        //                 return;
        //             }

        //             if (mode === "ORIG") {
        //                 this.originPlaceId = place.place_id;
        //             } else {
        //                 this.destinationPlaceId = place.place_id;
        //             }

        //             this.route();
        //         });
        //     }
        //     route() {
        //         if (!this.originPlaceId || !this.destinationPlaceId) {
        //             return;
        //         }

        //         const me = this;

        //         this.directionsService.route({
        //                 origin: {
        //                     placeId: this.originPlaceId
        //                 },
        //                 destination: {
        //                     placeId: this.destinationPlaceId
        //                 },
        //                 travelMode: this.travelMode,
        //             },
        //             (response, status) => {
        //                 if (status === "OK") {
        //                     me.directionsRenderer.setDirections(response);
        //                 } else {
        //                     window.alert("Directions request failed due to " + status);
        //                 }
        //             },
        //         );
        //     }
        // }

        // window.initMap = initMap;
    </script>
@endsection

@section('jsvendor')
    <script src="{{ asset('template/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
@endsection
