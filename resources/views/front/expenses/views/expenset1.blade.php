<div id="account-details" class="content" step="1">
    <div class="alert alert-primary mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 1</h6>
        <small>การออกเดินทาง</small>
    </div>
    <div class="row g-4">
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="text" id="expense_t1" class="form-control" value="{{ $expense->vbooking->location_name }}"
                    disabled>
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
                        @if ($expense->vbooking->type_reserve == 4)
                            <option value="1" {{ $expense->departurefrom == 1 ? 'selected' : '' }}>บริษัท</option>
                            <option value="2" {{ $expense->departurefrom == 2 ? 'selected' : '' }}>สถานที่พัก
                            </option>
                        @else
                            <option value="1" {{ $expense->departurefrom == 1 ? 'selected' : '' }}>บริษัท</option>
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
                    {{-- <input type="text" id="confirm-password" class="form-control" value="{{ $expense->vbooking->bu }}"
                        aria-describedby="confirm-password2"> --}}

                    @if ($expense->vbooking->locationid == 12)
                        @if ($expense->vbooking->type_reserve == 1)
                            @php
                                $selectedPlant = $plants->firstWhere('plantname', $expense->vbooking->bu);
                            @endphp
                            <select id="departureplant2" name="departureplant2" class="form-control w-100 text-dark"
                                data-style="btn-default" tabindex="null" disabled>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->id }}"
                                        {{ $plant->plantname == $expense->vbooking->bu ? 'selected' : '' }}>
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
                            $selectedPlant = $plants->firstWhere('plantname', $expense->vbooking->bu);
                        @endphp
                        <select id="departureplant2" name="departureplant2" class="form-control w-100 text-dark"
                            data-style="btn-default" tabindex="null" disabled>
                            @foreach ($plants as $plant)
                                <option value="{{ $plant->id }}"
                                    {{ $plant->plantname == $expense->vbooking->bu ? 'selected' : '' }}>
                                    {{ $plant->plantname }}
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
        <div class="col-sm-12" style="display:none;" id="MapDistanceModal">
            <input id="origin-input" class="controls" type="text" placeholder="Enter an origin location" />

            <input id="destination-input" class="controls" type="text" placeholder="Enter a destination location" />

            <div id="mode-selector" class="controls">
                <input type="radio" name="type" id="changemode-walking" checked="checked" />
                <label for="changemode-walking">Walking</label>

                <input type="radio" name="type" id="changemode-transit" />
                <label for="changemode-transit">Transit</label>

                <input type="radio" name="type" id="changemode-driving" />
                <label for="changemode-driving">Driving</label>
            </div>

            {{-- <div id="map"></div>

            <script
                src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places&v=weekly"
                defer></script> --}}
        </div>
        {{-- Map --}}
    </div>

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
                        @if ($expense->vbooking->type_reserve == 4 && $expense->vbooking->locationid == 12)
                            <option value="2" {{ $expense->returnfrom == 2 ? 'selected' : '' }}>สถานที่อื่นๆ
                            </option>
                        @elseif($expense->vbooking->type_reserve == 4 && $expense->locationid != 12)
                            <option value="1" {{ $expense->returnfrom == 1 ? 'selected' : '' }}>บริษัท</option>
                        @elseif($expense->vbooking->type_reserve == 1 && $expense->vbooking->locationid == 12)
                            <option value="2" {{ $expense->returnfrom == 2 ? 'selected' : '' }}>สถานที่อื่นๆ
                            </option>
                        @else
                            <option value="1" {{ $expense->returnfrom == 1 ? 'selected' : '' }}>บริษัท</option>
                        @endif

                    </select>
                    <label for="returnfrom">ออกเดินทางจาก</label>
                </div>

            </div>
        </div>

        <div class="col-sm-6 form-password-toggle">
            <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                    @if ($expense->vbooking->locationid == 12)
                        @if ($expense->vbooking->type_reserve == 1)
                            <input type="text" class="form-control" name="returnfromtext2"
                                value="{{ $expense->vbooking->location_name }}" disabled>
                            <input type="hidden" class="form-control" name="returnfromtext"
                                value="{{ $expense->vbooking->location_name }}">
                            <label for="returnfromtext2">รายละเอียดสถานที่</label>
                        @else
                        @endif
                    @else
                        @php
                            $selectedReturnPlant = $plants->firstWhere('plantname', $expense->vbooking->locationbu);
                        @endphp
                        <select id="returnplant2" name="returnplant2" class="form-control w-100 text-dark"
                            data-style="btn-default" tabindex="null" disabled>
                            @foreach ($plants as $plant)
                                <option value="{{ $plant->id }}"
                                    {{ $plant->plantname == $expense->vbooking->locationbu ? 'selected' : '' }}>
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
                <input type="text" id="returntime" name="returntime" class="form-control"
                    value="{{ $expense->returntime }}" required />
                <label for="returntime">ถึงเวลา</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="text" id="totaldistance_text" name="totaldistance_text" class="form-control"
                    value="{{ $expense->totaldistance }}" disabled>
                <input type="hidden" id="totaldistance" name="totaldistance" class="form-control"
                    value="{{ $expense->totaldistance }}">
                <label for="totaldistance">ระยะทางไป-กลับ</label>
            </div>
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
                        {{ $expense->vbooking->objname == $label ? 'checked disabled' : 'disabled' }}>
                    {{ $label }}
                </label>
            @endforeach


        </div>
        @if ($expense->vbooking->objid == 7)
            <div class="col-sm-12">
                <div class="form-floating form-floating-outline mb-4">
                    <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" disabled>{{ $expense->vbooking->remark }}</textarea>
                    <label for="exampleFormControlTextarea1">อื่่นๆ</label>
                </div>
            </div>
        @endif
        <div class="col-12 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary btn-prev waves-effect" disabled="">
                <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            <button type="button" class="btn btn-primary btn-next waves-effect waves-light">
                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                <i class="mdi mdi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
