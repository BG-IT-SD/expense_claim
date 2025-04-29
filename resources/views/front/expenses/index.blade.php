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
                        <form id="expensefrm" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- hidden --}}
                            <input type="hidden" name="extype" value="{{ $typegroup }}">
                            <input type="hidden" name="bookid" value="{{ $booking->id }}">
                            <input type="hidden" name="empid" value="{{ $empid }}">
                            <input type="hidden" name="passengertype" value="{{ $passengertype }}">
                            <input type="hidden" name="locationbu" value="{{ $booking->locationbu }}">
                            <input type="hidden" name="locationid" value="{{ $booking->locationid }}">
                            <input type="hidden" name="checktypereserve" id="checktypereserve"
                                value="{{ $booking->type_reserve }}">
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


        /* popup */
        .block-description {
            color: #ff4c51;
            margin-bottom: 16px;
        }


        .block-item {
            border: 2px solid #ffffff;
            border-radius: 30px;
            background: linear-gradient(270deg, #0F4CAF 0%, #A5C7FF 100%);
            box-shadow: 0 0.375rem 1rem 0 rgba(51, 55, 63, 0.12);
            letter-spacing: 1px;
        }

        .block-item .block-plant {
            display: grid;
            gap: 16px;
            background: #f0f8ff;
            border-radius: 20px;
            padding: 16px;
        }

        .block-item .block-content {
            padding-left: 20px;
        }


        .block-item .name {
            color: #000000;
            font-size: 22px;
            font-weight: 600;
        }

        .block-item p {
            color: #ffffff;
        }

        .block-title {
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1.2px;
            color: #000000;
        }

        .block-item .block-plant .img-logo {
            border-bottom-style: solid;
            padding-bottom: 16px;
            color: #ffffff;
        }

        .email {
            font-size: 22px;
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
    {{-- <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/map.js']) }}"></script> --}}
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/expense.js']) }}"></script>
    <script src="{{ URL::signedRoute('secure.js', ['filename' => 'js/expense/multi-step.js']) }}"></script>

    @if ($booking->type_reserve == 4)
        <script>
            // let map, directionsService, directionsRenderer;

            // function initMap() {
            //     map = new google.maps.Map(document.getElementById("map"), {
            //         zoom: 13,
            //         center: { lat: 13.7563, lng: 100.5018 }, // Bangkok
            //     });
            //     directionsService = new google.maps.DirectionsService();
            //     directionsRenderer = new google.maps.DirectionsRenderer({ map: map });
            // }

            let map, directionsService, directionsRenderer;
            let autocompleteOrigin, autocompleteDestination;
            let originPlace = null;
            let destinationPlace = null;

            function initMap() {
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 13,
                    center: {
                        lat: 13.7563,
                        lng: 100.5018
                    }
                });

                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map
                });

                const originInput = document.getElementById("origin");
                const destinationInput = document.getElementById("destination");

                autocompleteOrigin = new google.maps.places.Autocomplete(originInput);
                autocompleteDestination = new google.maps.places.Autocomplete(destinationInput);

                autocompleteOrigin.setComponentRestrictions({
                    country: ["th"]
                });
                autocompleteDestination.setComponentRestrictions({
                    country: ["th"]
                });

                // ✅ เก็บชื่อสถานที่ (name) ลง input
                autocompleteOrigin.addListener('place_changed', () => {
                    originPlace = autocompleteOrigin.getPlace();
                    document.getElementById("map_a_name").value = originPlace.name || originPlace.formatted_address;
                });

                autocompleteDestination.addListener('place_changed', () => {
                    destinationPlace = autocompleteDestination.getPlace();
                    document.getElementById("map_b_name").value = destinationPlace.name || destinationPlace
                        .formatted_address;
                });
            }
            // window.initMap = initMap;


            function calculateDistance() {
                const originInput = document.querySelector('#origin input');
                const destinationInput = document.querySelector('#destination input');

                const originAddress = originPlace?.formatted_address || originInput?.value;
                const destinationAddress = destinationPlace?.formatted_address || destinationInput?.value;

                if (!originAddress || !destinationAddress) {
                    alert("กรุณากรอกที่ตั้งต้นทางและปลายทางให้ครบ");
                    return;
                }


                // document.getElementById("map_a_name").value = originAddress;
                // document.getElementById("map_b_name").value = destinationAddress;


                if (originPlace?.geometry?.location) {
                    document.getElementById("latitude").value = originPlace.geometry.location.lat();
                    document.getElementById("longitude").value = originPlace.geometry.location.lng();
                }

                if (destinationPlace?.geometry?.location) {
                    document.getElementById("latitude_b").value = destinationPlace.geometry.location.lat();
                    document.getElementById("longitude_b").value = destinationPlace.geometry.location.lng();
                }

                directionsService.route({
                    origin: originAddress,
                    destination: destinationAddress,
                    travelMode: google.maps.TravelMode.DRIVING,
                }, (result, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(result);

                        const distanceValue = result.routes[0].legs[0].distance.value;
                        const distanceText = result.routes[0].legs[0].distance.text;

                        document.getElementById("distance").innerText = distanceText;
                        const km = ((distanceValue / 1000) * 2).toFixed(2);
                        document.getElementById("totaldistance_text").value = km;
                        document.getElementById("totaldistance").value = km;
                    } else {
                        alert("ไม่สามารถคำนวณเส้นทางได้ กรุณาตรวจสอบชื่อสถานที่ให้ชัดเจน");
                    }
                });
            }
        </script>

        <!-- โหลด Google Maps API -->
        {{-- <script async
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABibhL6u-A5s_G40-9tKSBNqT5P6s_iKU&callback=initMap&libraries=places&v=weekly&language=th">
        </script> --}}

        {{-- <script>
            let map, directionsService, directionsRenderer;
            let originAutocomplete, destinationAutocomplete;

            function initMap() {
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 13,
                    center: {
                        lat: 13.7563,
                        lng: 100.5018
                    }
                });

                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map
                });

                originAutocomplete = new google.maps.places.Autocomplete(document.getElementById("origin"));
                destinationAutocomplete = new google.maps.places.Autocomplete(document.getElementById("destination"));

                originAutocomplete.setComponentRestrictions({
                    country: ["th"]
                });
                destinationAutocomplete.setComponentRestrictions({
                    country: ["th"]
                });
            }

            window.initMap = initMap;

            function calculateDistance() {
                const origin = document.getElementById("origin").value;
                const destination = document.getElementById("destination").value;

                if (!origin || !destination) {
                    alert("กรุณากรอกที่ตั้งต้นทางและปลายทางให้ครบ");
                    return;
                }

                document.getElementById("origin_value").value = origin;
                document.getElementById("destination_value").value = destination;

                directionsService.route({
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING
                }, (result, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(result);

                        const distanceValue = result.routes[0].legs[0].distance.value;
                        const distanceText = result.routes[0].legs[0].distance.text;

                        document.getElementById("distance").innerText = distanceText;
                        const km = ((distanceValue / 1000) * 2).toFixed(2);
                        document.getElementById("totaldistance_text").value = km;
                        document.getElementById("totaldistance").value = km;
                    } else {
                        alert("ไม่สามารถคำนวณเส้นทางได้");
                    }
                });
            }
        </script>

        <script async
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABibhL6u-A5s_G40-9tKSBNqT5P6s_iKU&callback=initMap&libraries=places&v=weekly">
        </script> --}}
    @endif
@endsection
