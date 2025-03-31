<div id="social-links" class="content active dstepper-block">
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
                <input type="number" id="google" min="0" class="form-control"
                    placeholder="">
                <label for="google">รถโดยสารสาธารณะทั่วไป / บาท</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="linkedin" min="0" class="form-control"
                    placeholder="">
                <label for="linkedin">ค่าทางด่วน / บาท</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="other" min="0" class="form-control"
                    placeholder="">
                <label for="other">ค่าใช้จ่ายอื่นๆ / บาท</label>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <h5 class="card-header">Upload ไฟล์หลักฐาน</h5>
                <div class="card-body">
                    <form action="/upload" class="dropzone needsclick" id="dropzone-multi">
                        <div class="dz-message needsclick">
                            Drop files here or click to upload
                            <span class="note needsclick">(This is just a demo dropzone.
                                Selected files are
                                <span class="fw-medium">not</span> actually uploaded.)</span>
                        </div>
                        <div class="fallback">
                            <input name="file" type="file" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 3</h6>
        <small>ค่าน้ำมัน</small>
    </div>
    <div class="row g-4">
        <div class="col-sm-12 text-center">
            <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                    id="inlineRadio111" value="option111" >
                <label class="form-check-label" for="inlineRadio111">ประสงค์เบิกน้ำมัน</label>
            </div>
            <div class="form-check form-check-inline mt-3">
                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                    id="inlineRadio222" value="option222" checked="checked">
                <label class="form-check-label"
                    for="inlineRadio222">ไม่ประสงค์เบิกน้ำมัน</label>
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
                                        <div
                                            class="form-check form-check-inline form-check-primary">
                                            <input class="form-check-input" type="checkbox"
                                                id="inlineCheckbox3" value="option3">
                                        </div>
                                    </td>
                                    <td>45.04 - 50.03</td>
                                    <td>6</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                            class="form-check form-check-inline form-check-primary">
                                            <input class="form-check-input" type="checkbox"
                                                id="inlineCheckbox3" value="option3">
                                        </div>
                                    </td>
                                    <td>40.04 - 45.03</td>
                                    <td>5.5</td>
                                </tr>

                                <tr>
                                    <td>
                                        <div
                                            class="form-check form-check-inline form-check-primary">
                                            <input class="form-check-input" type="checkbox"
                                                id="inlineCheckbox3" value="option3"
                                                checked="checked">
                                        </div>
                                    </td>
                                    <td>30.05 - 40.03</td>
                                    <td>5</td>
                                </tr>

                                <tr>
                                    <td>
                                        <div
                                            class="form-check form-check-inline form-check-primary">
                                            <input class="form-check-input" type="checkbox"
                                                id="inlineCheckbox3" value="option3">
                                        </div>
                                    </td>
                                    <td>25.05 - 30.04</td>
                                    <td>4.5</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                            class="form-check form-check-inline form-check-primary">
                                            <input class="form-check-input" type="checkbox"
                                                id="inlineCheckbox3" value="option3">
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
                        <td>110.00</td>
                    </tr>
                    <tr>
                        <td>ค่าเดินทาง และ อื่นๆ</td>
                        <td>100.00</td>
                    </tr>
                    <tr>
                        <td>ค่าน้ำมัน</td>
                        <td>770.00</td>
                    </tr>
                    <tr>
                        <td>รวม</td>
                        <td><button type="button"
                                class="btn rounded-pill btn-success waves-effect waves-light">980.00</button>
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
                        <h5 class="card-title "><span
                                class="badge rounded-pill bg-primary">อนุมัติการเบิก</span>
                        </h5>
                        <hr>
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <select id="selectpickerBasic2" class="selectpicker w-100"
                                    data-style="btn-default" tabindex="null">
                                    <option>เลือกผู้อนุมัติ</option>
                                    <option>ผู้จัดการฝ่าย</option>
                                    <option>ผู้จัดการส่วนบริหาร</option>
                                </select>
                                <label for="password">ผู้อนุมัติ</label>
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