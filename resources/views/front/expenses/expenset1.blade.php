<div id="account-details" class="content" step="1">
    <div class="alert alert-primary mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 1</h6>
        <small>การออกเดินทาง</small>
    </div>
    <div class="row g-4">
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="text" id="expense_t1" class="form-control" value="{{ $booking->location_name }}" disabled>
                <label for="expense_t1">สถานที่ปฏิบัติงาน</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="text" id="expense_t2" class="form-control" value="{{ $departure_date }}" disabled>
                <input type="hidden" name="departuredatemail" value="{{ $departure_date . ' - ' . $return_date }}">
                <label for="expense_t2">วันเวลาที่ออกปฏิบัติงาน</label>
            </div>
        </div>
        <div class="col-sm-6 form-password-toggle">
            <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                    <select id="departurefrom" name="departurefrom" class="form-control w-100 text-dark"
                        data-style="btn-default" tabindex="null">
                        <!-- <option>เลือกสถานที่</option> -->
                        {{-- ถ้าเดินทางแบบรถส่วนตัวให้เลือกทั้งสองรายการ แต่ถ้าเดินทางจากรถบริษัทมีแค่บริษัทอย่างเดียว --}}
                        @if ($booking->type_reserve == 4)
                            <option value="1">บริษัท</option>
                            <option value="2">สถานที่พัก</option>
                        @else
                            <option value="1">บริษัท</option>
                        @endif

                    </select>
                    <label for="departurefrom">ออกเดินทางจาก</label>
                </div>

            </div>
        </div>

        {{-- กรณีไป Plant ปกติ --}}
        <div class="col-sm-6 form-password-toggle">
            <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                    {{-- <input type="text" id="confirm-password" class="form-control" value="{{ $booking->bu }}"
                        aria-describedby="confirm-password2">
                    --}}
                    @if ($booking->locationid == 12)
                        @if ($booking->type_reserve == 1 || $booking->type_reserve == 3)
                            @php
                                $selectedPlant = $plants->firstWhere('plantname', $booking->bu);
                            @endphp
                            <select id="departureplant2" name="departureplant2" class="form-control w-100 text-dark"
                                data-style="btn-default" tabindex="null" disabled>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}"
                                        {{ $plant->plantname == $booking->bu ? 'selected' : '' }}>
                                        {{ $plant->plantname }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="departureplant" value="{{ $selectedPlant?->id }}">
                            <label for="departureplant">รายละเอียดสถานที่</label>
                        @else
                        @endif
                    @else
                        @php
                            $selectedPlant = $plants->firstWhere('plantname', $booking->bu);
                        @endphp
                        <select id="departureplant2" name="departureplant2" class="form-control w-100 text-dark"
                            data-style="btn-default" tabindex="null" disabled>
                            @foreach ($plants as $plant)
                                <option value="{{ $plant->id }}"
                                    {{ $plant->plantname == $booking->bu ? 'selected' : '' }}>{{ $plant->plantname }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="departureplant" value="{{ $selectedPlant?->id }}">
                        <label for="departureplant">รายละเอียดสถานที่</label>
                    @endif

                </div>

            </div>
        </div>
        {{-- Map --}}
        @if ($booking->type_reserve == 4 && $passengertype == 0)
            <!-- เฉพาะเมื่อ type_reserve == 4 -->
            <div class="container mt-3 mb-3">
                <h3>กรุณาเลือกสถานที่ต้นทางและปลายทางเพื่อคำนวณระยะทาง</h3>
                <div class="mb-3">
                    <label for="origin">ต้นทาง</label>
                    <input id="origin" class="form-control" type="text" placeholder="เช่น บางกอกกล๊าส">
                </div>
                <div class="mb-3">
                    <label for="destination">ปลายทาง</label>
                    <input id="destination" class="form-control" type="text" placeholder="เช่น ฟิวเจอร์พาร์ค">
                </div>
                <button onclick="calculateDistance()" class="btn btn-primary">คำนวณระยะทาง</button>

                <div class="mt-3">
                    <strong>ระยะทาง:</strong> <span id="distance"></span>
                </div>

                <div id="map" style="height: 400px;" class="mt-4"></div>
                {{-- เก็บ ละติจูด ลองติจูด และชื้อปลายทางและต้นทาง --}}
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="latitude_b" id="latitude_b">
                <input type="hidden" name="longitude_b" id="longitude_b">
                <input type="hidden" name="map_a_name" id="map_a_name">
                <input type="hidden" name="map_b_name" id="map_b_name">
            </div>
            {{-- <div class="container mt-3 mb-3">
                <h3>กรุณาเลือกสถานที่ต้นทางและปลายทางเพื่อคำนวณระยะทาง</h3>

                <div class="mb-3">
                    <label for="origin">ต้นทาง</label>
                    <gmp-place-autocomplete id="origin"></gmp-place-autocomplete>
                    <input type="hidden" name="origin_value" id="origin_value">
                </div>

                <div class="mb-3">
                    <label for="destination">ปลายทาง</label>
                    <gmp-place-autocomplete id="destination"></gmp-place-autocomplete>
                    <input type="hidden" name="destination_value" id="destination_value">
                </div>

                <button onclick="calculateDistance()" class="btn btn-primary">คำนวณระยะทาง</button>

                <div class="mt-3">
                    <strong>ระยะทาง:</strong> <span id="distance"></span>
                </div>

                <div id="map" style="height: 400px;" class="mt-4"></div>

            </div> --}}
        @endif


        {{-- Map --}}
    </div>
    {{--
            <script
                src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places&v=weekly"
                defer></script> --}}

    <div class="alert alert-primary mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 1</h6>
        <small>เดินทางกลับ</small>
    </div>
    <div class="row g-4">

        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="text" id="expense_t3" class="form-control" value="{{ $return_date }}" disabled>
                <label for="expense_t3">วันเวลาที่เดินทางกลับ</label>
            </div>
        </div>

        <div class="col-sm-6 form-password-toggle">
            <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                    <select id="returnfrom" name="returnfrom" class="form-control w-100 text-dark"
                        data-style="btn-default" tabindex="null">
                        @if ($booking->type_reserve == 4 && $booking->locationid == 12)
                            {{-- รถส่วนตัวและนอกสถานที่ --}}
                            <option value="2">สถานที่อื่นๆ</option>
                        @elseif($booking->type_reserve == 4 && $booking->locationid != 12)
                            <option value="1">บริษัท</option>
                        @elseif(($booking->type_reserve == 1 || $booking->type_reserve == 3) && $booking->locationid == 12)
                            <option value="2">สถานที่อื่นๆ</option>
                        @else
                            <option value="1">บริษัท</option>
                        @endif

                    </select>
                    <label for="returnfrom">ออกเดินทางจาก</label>
                </div>

            </div>
        </div>

        <div class="col-sm-6 form-password-toggle">
            <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                    @if ($booking->locationid == 12)
                        @if ($booking->type_reserve == 1 || $booking->type_reserve == 3 || $booking->type_reserve == 4)
                            <input type="text" class="form-control" name="returnfromtext2"
                                value="{{ $booking->location_name }}" disabled>
                            <input type="hidden" class="form-control" name="returnfromtext"
                                value="{{ $booking->location_name }}">
                            <label for="returnfromtext2">รายละเอียดสถานที่</label>
                        @else
                        @endif
                    @else
                        @php
                            $selectedReturnPlant = $plants->firstWhere('plantname', $booking->locationbu);
                        @endphp
                        <select id="returnplant2" name="returnplant2" class="form-control w-100 text-dark"
                            data-style="btn-default" tabindex="null" disabled>
                            @foreach ($plants as $plant)
                                <option value="{{ $plant->id }}"
                                    {{ $plant->plantname == $booking->locationbu ? 'selected' : '' }}>
                                    {{ $plant->plantname }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="returnplant" value="{{ $selectedReturnPlant?->id }}">
                    @endif
                    <label for="returnplant">รายละเอียดสถานที่</label>
                </div>

            </div>
        </div>
        <div class="col-sm-6">
            {{-- <div class="form-floating form-floating-outline">
                <input type="time" id="returntime" name="returntime" class="form-control" value="">
                <label for="returntime">ถึงเวลา</label>
            </div> --}}
            <div class="form-floating form-floating-outline">
                <input type="text" id="returntime" name="returntime" placeholder="20:00:00" class="form-control"
                    required />
                <label for="returntime">ถึงเวลา</label>
            </div>
        </div>
        <div class="col-sm-6">
            @if ($booking->type_reserve == 1 || $booking->type_reserve == 3)
                <div class="form-floating form-floating-outline">
                    <input type="text" id="totaldistance_text" name="totaldistance_text" class="form-control"
                        value="{{ $totalDistance }}" disabled>
                    <input type="hidden" id="totaldistance" name="totaldistance" class="form-control"
                        value="{{ $totalDistance }}">
                    <label for="totaldistance_text">ระยะทางไป-กลับ</label>
                </div>
            @else
                <div class="form-floating form-floating-outline">
                    <input type="text" id="totaldistance_text" name="totaldistance_text" class="form-control"
                        value="0" disabled>
                    <input type="hidden" id="totaldistance" name="totaldistance" class="form-control"
                        value="0">
                    <input type="hidden" id="totaldistancemax" name="totaldistancemax" class="form-control"
                        value="{{ $totalDistance }}">
                    <label for="totaldistance_text">ระยะทางไป-กลับ</label>
                </div>
            @endif
        </div>
    </div>
    <div class="alert alert-primary mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 1</h6>
        <small>วัตถุประสงค์</small>
    </div>
    <div class="row g-4">
        <div class="col-sm-12">
            @foreach ($reasons as $key => $label)
                <label class="form-check-inline me-3">
                    <input class="form-check-input" type="radio" name="purpose" value="{{ $label }}"
                        {{ $booking->objname == $label ? 'checked disabled' : 'disabled' }}>
                    {{ $label }}
                </label>
            @endforeach


        </div>
        @if ($booking->objid == 7)
            <div class="col-sm-12">
                <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" disabled>{{ $booking->remark }}</textarea>
                    <label for="exampleFormControlTextarea1">อื่่นๆ</label>
                </div>
            </div>
        @endif
        <div class="col-12 d-flex justify-content-between">
            <button class="btn btn-outline-secondary btn-prev waves-effect" disabled="">
                <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            <button class="btn btn-primary btn-next waves-effect waves-light">
                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                <i class="mdi mdi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
