<div id="social-links" class="content active dstepper-block" step="3">
    {{-- <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 3</h6>
        <small>รายชื่อพนักงาน</small>
    </div> --}}
    {{-- <div class="row g-4">
        <div class="col-sm-12">
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ชื่อ - นามสกุล</th>
                                <th>รหัสพนักงาน</th>
                                <th>Status</th>
                                <th>Expense ID</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <i
                                        class="mdi mdi-account mdi-20px text-danger me-3"></i><span
                                        class="fw-medium">กมลวรรณ บรรชา</span>
                                </td>
                                <td>66000510</td>

                                <td><span
                                        class="badge rounded-pill bg-label-primary me-1">Active</span>
                                </td>
                                <td>-</td>

                            </tr>
                            <tr>
                                <td>
                                    <i class="mdi mdi-account mdi-20px text-info me-3"></i><span
                                        class="fw-medium">เสาวภา เข็มเหลือง</span>
                                </td>
                                <td>63000455</td>

                                <td><span
                                        class="badge rounded-pill bg-label-success me-1">Completed</span>
                                </td>
                                <td><span
                                        class="badge rounded-pill bg-label-dark me-1">EX20241102</span>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
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
    {{-- @if ($booking->type_reserve == 4) --}}
        {{-- น้ำมัน --}}
        <div class="alert alert-dark mb-3 mt-3">
            <h6 class="mb-0">ส่วนที่ 3</h6>
            <small>ค่าน้ำมัน</small>
        </div>
        <div class="row g-4">
            <div class="col-sm-12 text-center">
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio111"
                        value="option111">
                    <label class="form-check-label" for="inlineRadio111">ประสงค์เบิกน้ำมัน</label>
                </div>
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio222"
                        value="option222" checked="checked">
                    <label class="form-check-label" for="inlineRadio222">ไม่ประสงค์เบิกน้ำมัน</label>
                </div>
            </div>
            <div class="col-sm-12" style="display: none;">
                <div class="card">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="card-header">Rate อัตราค่าน้ำมัน</h5>
                        </div>
                        <div class="col-sm-6 pt-3">
                            <h5><span class="badge rounded-pill bg-label-primary me-1">34.12</span>
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
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-inline form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                    value="option3">
                                            </div>
                                        </td>
                                        <td>45.04 - 50.03</td>
                                        <td>6</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-inline form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                    value="option3">
                                            </div>
                                        </td>
                                        <td>40.04 - 45.03</td>
                                        <td>5.5</td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="form-check form-check-inline form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                    value="option3" checked="checked">
                                            </div>
                                        </td>
                                        <td>30.05 - 40.03</td>
                                        <td>5</td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="form-check form-check-inline form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                    value="option3">
                                            </div>
                                        </td>
                                        <td>25.05 - 30.04</td>
                                        <td>4.5</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-inline form-check-primary">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                    value="option3">
                                            </div>
                                        </td>
                                        <td>20.05 - 25.04</td>
                                        <td>4</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" style="display: none;">
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
                            <td>70.7</td>
                            <td>5</td>
                            <td>707</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- น้ำมัน --}}
    {{-- @endif --}}
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
                        <td><span
                                class="btn rounded-pill btn-primary waves-effect waves-light totallastfood">0</span>
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
        {{--

        เงื่อนไขที่ 1 อนุมัติตัวเองกรณี level >= 10 และอยู่ในกลุ่มเลขา
        if($oHeadEmp['head_emp_id'] == "41000014" || $LEVEL >= 10 || $approve_g > 0){
        }else{
            เงื่อนไขที่ 2 level < 10
            if($oHeadEmp['head_emp_id'] == ""){
            เงื่อนไขที่ 3 ถ้าไม่มีหัวหน้า ให้เลือกคนที่จะ approve แต่ต้องมากกว่าเลเวล 7
            }else{
             เงื่อนไขที่ 4 มีหัวหน้า
                if($head_Level > 10){
                เงื่อนไขที่ 5 มีหัวหน้าและหัวหน้ามากกว่าเลเวล 10 ห้เลือกคนที่จะ approve แต่ต้องมากกว่าเลเวล 7
                }else{
                เงื่อนไขที่ 6 มีหัวหน้า และขึ้นชื่อหัวหน้าตาม v_head_emp
                }
            }
        }
        --}}
        <div class="row g-4">
            <div class="col-sm-12">
                <div class="card shadow-none bg-transparent border border-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title "><span class="badge rounded-pill bg-primary">อนุมัติการเบิก</span>
                        </h5>
                        <hr>
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                {{-- @php
                                    $finalHEmail = '';
                                    $finalHName = '';
                                    $finalId = '';
                                @endphp --}}
                                <select id="headapprove" class="select2 form-select form-select-l"
                                    data-style="btn-default" tabindex="null">
                                    {{-- เงื่อนไขที่ 1 อนุมัติตัวเองกรณี level >= 10 และอยู่ในกลุ่มเลขา --}}
                                    {{-- @if ($headlevel >= 10 || $approve_g > 0)
                                        @php
                                            $finalHEmail = $empemail;
                                            $finalHName = $empfullname;
                                            $finalId = $empid;
                                        @endphp
                                        <option value="{{ $empid }}">{{ $empemail . ' | ' . $empfullname }}
                                        </option>
                                    @else --}}
                                        {{-- เงื่อนไขที่ 2 level < 10 --}}
                                        {{-- @if ($headempid == '') --}}
                                            {{-- เงื่อนไขที่ 3 ถ้าไม่มีหัวหน้า ให้เลือกคนที่จะ approve แต่ต้องมากกว่าเลเวล 7 --}}
                                            {{-- <option value="">เลือกผู้อนุมัติ</option> --}}
                                        {{-- @else --}}
                                            {{-- งื่อนไขที่ 4 มีหัวหน้า --}}
                                            {{-- @if ($headlevel > 10) --}}
                                                {{-- เงื่อนไขที่ 5 มีหัวหน้าและหัวหน้ามากกว่าเลเวล 10 ห้เลือกคนที่จะ approve แต่ต้องมากกว่าเลเวล 7 --}}
                                                {{-- <option value="">เลือกผู้อนุมัติ</option> --}}
                                            {{-- @else
                                                @php
                                                    $finalHEmail = $heademail;
                                                    $finalHName = $headname;
                                                    $finalId = $headempid;
                                                @endphp --}}
                                                {{-- เงื่อนไขที่ 6 มีหัวหน้า และขึ้นชื่อหัวหน้าตาม v_head_emp --}}
                                                {{-- <option value="{{ $headempid }}">
                                                    {{ $heademail . ' | ' . $headname }}
                                                </option>
                                            @endif
                                        @endif

                                    @endif

                                </select>
                                <label for="password">ผู้อนุมัติ</label>
                                <input type="hidden" name="head_email" id="head_email" value="{{ $finalHEmail }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="head_name" id="head_name" value="{{ $finalHName }}"
                                    class="form-control form-control-input">
                                <input type="hidden" name="head_id" id="head_id" value="{{ $finalId }}"
                                    class="form-control form-control-input"> --}}
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
