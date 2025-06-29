<div id="social-links" class="content active dstepper-block" step="3">
    <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 3</h6>
        <small>ค่าใช้จ่ายอื่นๆเกี่ยวกับการเดินทาง</small>
    </div>
    <div class="row g-4">
        {{-- <div class="col-sm-12 text-center">
            <img src="{{ asset('storage/images/ratekm.png') }}" width="75%" alt="">

        </div> --}}

        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="publictransportfare" name="publictransportfare" min="0"
                    class="form-control" value="0">
                <label for="publictransportfare">รถโดยสารสาธารณะทั่วไป / บาท</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="expresswaytoll" name="expresswaytoll" min="0" class="form-control"
                    value="0">
                <label for="expresswaytoll">ค่าทางด่วน / บาท</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="otherexpenses" name="otherexpenses" min="0" class="form-control"
                    value="0">
                <label for="otherexpenses">ค่าใช้จ่ายอื่นๆ / บาท</label>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card ">
                <h5 class="card-header">Upload ไฟล์หลักฐาน</h5>
                <div class="card-body row">
                    {{-- <div id="dropzone-multi" class="dropzone needsclick">
                        <div class="dz-message needsclick">
                            Drop files here or click to upload
                            <span class="note needsclick">(This is just a demo dropzone. Selected files are not actually
                                uploaded.)</span>
                        </div>
                    </div> --}}
                    <!-- input แบบ multiple -->
                    {{-- <div class="mb-3">
                        <label for="fileInput" class="form-label">Upload ไฟล์หลักฐาน</label>
                        <input type="file" name="files[]" id="fileInput" class="form-control" multiple>
                        <div id="fileList" class="mt-2"></div>
                    </div> --}}
                    <div class="col-md-8">
                        <div id="file-container">
                            <div class="file-row mb-2 d-flex gap-2 align-items-center">
                                <input type="file" name="files[]" class="form-control w-75">
                                <button type="button" class="btn btn-danger btn-remove">ลบ</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4"><button type="button" id="add-file"
                            class="btn btn-secondary mb-3">เพิ่มไฟล์</button>
                    </div>



                </div>
            </div>
        </div>
    </div>
    @if ($booking->type_reserve == 4 && $passengertype == 0)
        {{-- น้ำมัน --}}
        <div class="alert alert-dark mb-3 mt-3">
            <h6 class="mb-0">ส่วนที่ 3</h6>
            <small>ค่าน้ำมัน</small>
        </div>
        <div class="row g-4">
            <div class="col-sm-12 text-center">
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input checktoil" type="radio" name="checktoil" id="checktoil_1"
                        value="1">
                    <label class="form-check-label" for="checktoil_1">ประสงค์เบิกน้ำมัน</label>
                </div>
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-inpu checktoilt" type="radio" name="checktoil" id="checktoil_2"
                        value="2" checked="checked">
                    <label class="form-check-label" for="checktoil_2">ไม่ประสงค์เบิกน้ำมัน</label>
                </div>
            </div>
            <div class="col-sm-12" style="display: none;" id="rateoil">
                <div class="card">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="card-header">Rate อัตราค่าน้ำมัน</h5>
                        </div>
                        <div class="col-sm-6 pt-3">

                            <h5><span class="badge rounded-pill bg-label-danger me-1">วันที่การเดินทาง :
                                    {{ $departure_date }}</span>
                            </h5>
                            <h5><span class="badge rounded-pill bg-label-primary me-1">ราคาน้ำมัน ณ วันนั้น :
                                    {{ $data_oil_price }}</span>
                                <input type="hidden" name="fuel91id" value="{{ $oilid }}">
                            </h5>
                        </div>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr class="table-primary">
                                        <th></th>
                                        <th>
                                            <h6>ช่วงราคาน้ำมัน<br>(แก๊สโซฮอล 91)</h6>
                                        </th>
                                        <th>
                                            <h6>บาท/กิโลเมตร</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ratefuels as $ratefuel)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-inline form-check-primary">
                                                    <input class="form-check-input checkfuel" type="checkbox"
                                                        value="{{ $ratefuel->id }}"
                                                        {{ isset($rate_id) && $ratefuel->id == $rate_id ? 'checked' : '' }}
                                                        onclick="return false;">
                                                </div>
                                            </td>
                                            <td>{{ $ratefuel->startrate . ' - ' . $ratefuel->endrate }}</td>
                                            <td>{{ $ratefuel->bathperkm }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" style="display: none;" id="allsumoil">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr class="table-danger">
                            <th>
                                <h6>ระยะทาง (กิโลเมตร)</h6>
                            </th>
                            <th>
                                <h6>อัตราค่าน้ำมัน</h6>
                            </th>
                            <th>
                                <h6>รวม / บาท</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <h5><span class="badge rounded-pill bg-danger lastkm"> </span></h5>
                                <span id="max-distance-label" style="display:none; color:red;">
                                    *ใช้ระยะทางสูงสุดที่อนุญาต
                                </span>
                                <span id="base-distance-distance-label" style="display:none; color:red;">
                                </span>
                                <input type="hidden" id="afdistance" name="afdistance" value="0" />
                                <input type="hidden" id="base_distance" name="basedistance" value="0">
                            </td>
                            <td>
                                <h5><span class="badge rounded-pill bg-danger">{{ $bath_per_km }} บาท</span></h5>
                                <input type="hidden" id="bath_per_km" name="bath_per_km"
                                    value="{{ $bath_per_km }}">
                                <input type="hidden" id="fuelpricesid" name="fuelpricesid"
                                    value="{{ $rate_id }}">
                            </td>
                            <td>
                                <h5><span class="badge rounded-pill bg-success pricesuccess"> </span></h5>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="number" id="distancemore" name="distancemore" min="0"
                                class="form-control" value="0">
                            <label for="distancemore">ระยะทางเพิ่มเติม / กิโลเมตร</label>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3"></div>
                    <div class="col-md-12 mb-3">
                        <div class="form-floating form-floating-outline mb-4">
                            <textarea class="form-control h-px-100" id="distancenote" name="distancenote"
                                placeholder="ข้อมูลเพิ่มเติมเกี่ยวกับการเดินทาง"></textarea>
                            <label for="distancenote">ข้อมูลเพิ่มเติมเกี่ยวกับการเดินทาง</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- น้ำมัน --}}
    @endif
    <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 3</h6>
        <small>รวมค่าใช้จ่าย</small>
    </div>
    <div class="row g-4">
        <div class="col-sm-12">
            <table class="table table-bordered text-center">
                <thead>
                    <tr class="table-info">
                        <th>
                            <h6>รายละเอียด</h6>
                        </th>
                        <th>
                            <h6>จำนวนเงินขอเบิก / บาท</h6>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ค่าอาหาร</td>
                        <td><span class="btn rounded-pill btn-primary waves-effect waves-light totallastfood">0</span>
                        </td>
                    </tr>
                    <tr>
                        <td>ค่าเดินทาง และ อื่นๆ</td>
                        <td><span class="btn rounded-pill btn-primary waves-effect waves-light totaltravel">0</span>
                            <input type="hidden" class="expense-value" id="travelexpenses" name="travelexpenses"
                                value="0">
                        </td>
                    </tr>
                    <tr>
                        <td>ค่าน้ำมัน</td>
                        <td><span class="btn rounded-pill btn-primary waves-effect waves-light gasolinecost">0</span>
                            <input type="hidden" class="expense-value" id="gasolinecost" name="gasolinecost"
                                value="0">
                        </td>
                    </tr>
                    <tr>
                        <td>รวม</td>
                        <td><span class="btn rounded-pill btn-success waves-effect waves-light totalExpense">0</span>
                            <input type="hidden" id="totalExpense" name="totalExpense" value="0">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- Apporve for Head --}}
        <div class="row g-4">
            <div class="col-sm-12">
                <div class="card shadow-none bg-transparent border border-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title "><span class="badge rounded-pill bg-primary">อนุมัติการเบิก</span>
                        </h5>
                        <hr>
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                @php
                                    $finalHEmail = '';
                                    $finalHName = '';
                                    $finalId = '';
                                @endphp
                                <select id="headapprove" class="select2 form-select form-select-l"
                                    data-style="btn-default" tabindex="null">
                                    {{-- เงื่อนไขที่ 1 อนุมัติตัวเองกรณี อยู่ในกลุ่มเลขา --}}
                                    @if ($approve_g > 0)
                                        @php
                                            $finalHEmail = $empemail;
                                            $finalHName = $empfullname;
                                            $finalId = $empid;
                                        @endphp
                                        <option value="{{ $empid }}">{{ $empemail . ' | ' . $empfullname }}
                                        </option>
                                    @else
                                        {{-- เงื่อนไขที่ 2 level < 10 --}}
                                        @if ($headempid == '')
                                            {{-- เงื่อนไขที่ 3 ถ้าไม่มีหัวหน้า ให้เลือกคนที่จะ approve แต่ต้องมากกว่าเลเวล 8 --}}
                                            <option value="">เลือกผู้อนุมัติ</option>
                                        @else
                                            {{-- เงื่อนไขที่ New มีหัวหน้าและสืบLevel == 10 --}}
                                            @php
                                                $finalHEmail = $heademail;
                                                $finalHName = $headname;
                                                $finalId = $headempid;
                                            @endphp
                                            <option value="{{ $headempid }}">
                                                {{ $heademail . ' | ' . $headname }}
                                            </option>
                                            {{-- Old --}}
                                                {{-- เงื่อนไขที่ 4 มีหัวหน้า --}}
                                                {{-- @if ($headlevel >= 10) --}}
                                                {{-- เงื่อนไขที่ 5 มีหัวหน้าและหัวหน้ามากกว่าเลเวล 10 ห้เลือกคนที่จะ approve แต่ต้องมากกว่าเลเวล 8 --}}
                                                {{-- <option value="">เลือกผู้อนุมัติ</option>
                                                @else
                                                    @php
                                                        $finalHEmail = $heademail;
                                                        $finalHName = $headname;
                                                        $finalId = $headempid;
                                                    @endphp --}}
                                                {{-- เงื่อนไขที่ 6 มีหัวหน้า และขึ้นชื่อหัวหน้าตาม v_head_emp --}}
                                                {{-- <option value="{{ $headempid }}">
                                                        {{ $heademail . ' | ' . $headname }}
                                                    </option>
                                                @endif --}}
                                            {{-- Old --}}
                                        @endif

                                    @endif

                                </select>
                                <label for="password">ผู้อนุมัติ</label>
                                <input type="hidden" name="head_email" id="head_email" value="{{ $finalHEmail }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="head_name" id="head_name" value="{{ $finalHName }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="head_id" id="head_id" value="{{ $finalId }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="empleveldata" id="empleveldata"
                                    value="{{ $empLevel }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Apporve for Head --}}
        <div class="col-12 d-flex justify-content-between">
            <button class="btn btn-outline-secondary btn-prev waves-effect">
                <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            <button class="btn btn-primary btn-submit waves-effect waves-light">Submit</button>
        </div>
    </div>
</div>
