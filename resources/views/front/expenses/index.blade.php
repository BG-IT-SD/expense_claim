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
                                        <span class="bs-stepper-subtitle">Booking ID : 99999</span>
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
                            <!-- Account Details -->
                            <div id="account-details" class="content">
                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 1</h6>
                                    <small>การออกเดินทาง</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="username" class="form-control" value="AGI">
                                            <label for="username">สถานที่ปฏิบัติงาน</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="email" class="form-control"
                                                value="11/11/2024 8:30 น." aria-label="john.doe">
                                            <label for="email">วันเวลาที่ออกปฏิบัติงาน</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <select id="locationout" name="locationout" class="selectpicker w-100"
                                                    data-style="btn-default" tabindex="null">
                                                    <!-- <option>เลือกสถานที่</option> -->
                                                    <option value="1">บริษัท</option>
                                                    <option value="2">สถานที่พัก</option>
                                                </select>
                                                <label for="locationout">ออกเดินทางจาก</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-sm-6 form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="confirm-password" class="form-control"
                                                    value="PTI" aria-describedby="confirm-password2">
                                                <label for="confirm-password">รายละเอียดสถานที่</label>
                                            </div>

                                        </div>
                                    </div>
                                    {{-- Map --}}
                                    <div class="col-sm-12" style="display:none;" id="MapDistanceModal">
                                        <input
                                        id="origin-input"
                                        class="controls"
                                        type="text"
                                        placeholder="Enter an origin location"
                                      />

                                      <input
                                        id="destination-input"
                                        class="controls"
                                        type="text"
                                        placeholder="Enter a destination location"
                                      />

                                      <div id="mode-selector" class="controls">
                                        <input
                                          type="radio"
                                          name="type"
                                          id="changemode-walking"
                                          checked="checked"
                                        />
                                        <label for="changemode-walking">Walking</label>

                                        <input type="radio" name="type" id="changemode-transit" />
                                        <label for="changemode-transit">Transit</label>

                                        <input type="radio" name="type" id="changemode-driving" />
                                        <label for="changemode-driving">Driving</label>
                                      </div>

                                        <div id="map"></div>

                                        <script
                                            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places&v=weekly"
                                            defer>
                                        </script>
                                    </div>
                                    {{-- Map --}}
                                </div>

                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 1</h6>
                                    <small>เดินทางกลับ</small>
                                </div>
                                <div class="row g-4">

                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="email" class="form-control"
                                                value="11/11/2024 15:30 น." aria-label="john.doe">
                                            <label for="email">วันเวลาที่เดินทางกลับ</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <select id="selectpickerBasic" class="selectpicker w-100"
                                                    data-style="btn-default" tabindex="null">
                                                    <!-- <option>เลือกสถานที่</option> -->
                                                    <option>บริษัท</option>
                                                    <option>สถานที่พัก</option>
                                                </select>
                                                <label for="password">ออกเดินทางจาก</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-sm-6 form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="confirm-password" class="form-control"
                                                    value="PTI" aria-describedby="confirm-password2">
                                                <label for="confirm-password">รายละเอียดสถานที่</label>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="email" class="form-control" value="15:30 น."
                                                aria-label="john.doe">
                                            <label for="email">ถึงเวลา</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="email" class="form-control" value="70.7"
                                                aria-label="john.doe">
                                            <label for="email">ระยะทางไป-กลับ</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 1</h6>
                                    <small>วัตถุประสงค์</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-12">
                                        <div class="form-check form-check-inline mt-1">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio1" value="option1">
                                            <label class="form-check-label" for="inlineRadio1">อบรม</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio2" value="option2">
                                            <label class="form-check-label" for="inlineRadio2">สัมมนา</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio3" value="option3">
                                            <label class="form-check-label" for="inlineRadio3">ฝึกงาน</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio4" value="option4">
                                            <label class="form-check-label" for="inlineRadio4">ติดตั้งเครื่องจักร</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio5" value="option5">
                                            <label class="form-check-label" for="inlineRadio5">ลูกค้าร้องเรียน</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio6" value="option6">
                                            <label class="form-check-label" for="inlineRadio6">พบลูกค้า</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio7" value="option7" checked>
                                            <label class="form-check-label" for="inlineRadio7">อื่นๆ</label>
                                        </div>

                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <textarea class="form-control h-px-100" id="exampleFormControlTextarea1">Support System AGI</textarea>
                                            <label for="exampleFormControlTextarea1">อื่่นๆ</label>
                                        </div>
                                    </div>
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
                            <!-- Personal Info -->
                            <div id="personal-info" class="content">
                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 2</h6>
                                    <small>รายชื่อพนักงาน</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <!-- <h5 class="card-header">Striped rows</h5> -->
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
                                                                <i
                                                                    class="mdi mdi-account mdi-20px text-info me-3"></i><span
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
                                </div>
                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 2</h6>
                                    <small>เงื่อนไขมื้ออาหาร</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <!-- <h5 class="card-header">Bordered Table</h5> -->
                                            <div class="card-body">
                                                <div class="card-header border border-info">
                                                    <h5><span class="badge rounded-pill bg-dark"><span
                                                                class="mdi mdi-calendar-month-outline"></span> 11/11/2024
                                                            8:30 น. - 11/11/2024 15:30 น.</span></h5>
                                                </div>
                                                <div class="table-responsive text-nowrap">
                                                    <table class="table table-bordered text-center">
                                                        <thead>
                                                            <tr class="table-info">
                                                                <th>
                                                                    <h5>รายละเอียด</h5>
                                                                </th>
                                                                <th>
                                                                    <h5>มื้อเช้า</h5>
                                                                </th>
                                                                <th>
                                                                    <h5>มื้อกลางวัน</h5>
                                                                </th>
                                                                <th>
                                                                    <h5>มื้อเย็น</h5>
                                                                </th>
                                                                <th>
                                                                    <h5>มื้อดึก</h5>
                                                                </th>
                                                                <th>
                                                                    <h5>รวม</h5>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <i
                                                                        class="mdi mdi-food-outline mdi-20px text-danger me-3"></i><span
                                                                        class="fw-medium">เบิกมื้ออาหาร</span>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-success">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox1" value="option1"
                                                                            checked="checked">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-success">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox2" value="option2"
                                                                            checked="checked">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-success">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox3" value="option3"
                                                                            checked="checked">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-success">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox4" value="option4"
                                                                            checked="checked">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge rounded-pill bg-label-success me-1">4
                                                                        มื้อ</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <i
                                                                        class="mdi mdi-domain mdi-20px text-info me-3"></i><span
                                                                        class="fw-medium">บริษัทฯ จัดอาหารให้</span>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-danger">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox11" value="option11">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-danger">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox22" value="option22">
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-danger">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox33" value="option33">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="form-check form-check-inline form-check-danger">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            id="inlineCheckbox44" value="option44">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge rounded-pill bg-label-danger me-1">2
                                                                        มื้อ</span>
                                                                </td>
                                                            </tr>
                                                            <tr class="table-info">
                                                                <td>
                                                                    <i
                                                                        class="mdi mdi-currency-usd mdi-20px text-info me-3"></i><span
                                                                        class="fw-medium">รวม</span>
                                                                </td>
                                                                <td>50</td>
                                                                <td>60</td>
                                                                <td>0</td>
                                                                <td>0</td>
                                                                <td>110</td>
                                                            </tr>


                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary btn-prev waves-effect">
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
                            <!-- Social Links -->
                            <div id="social-links" class="content active dstepper-block">
                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 3</h6>
                                    <small>รายชื่อพนักงาน</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <!-- <h5 class="card-header">Striped rows</h5> -->
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
                                                                <i
                                                                    class="mdi mdi-account mdi-20px text-info me-3"></i><span
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
                                </div>
                                <div class="alert alert-dark mb-3 mt-3">
                                    <h6 class="mb-0">ส่วนที่ 3</h6>
                                    <small>ค่าใช้จ่ายอื่นๆเกี่ยวกับการเดินทาง</small>
                                </div>
                                <div class="row g-4">
                                    <div class="col-sm-12 text-center">
                                        <img src="{{ asset('storage/images/ratekm.png') }}" width="75%"
                                            alt="">

                                    </div>
                                    <div class="col-sm-12">
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
                                                id="inlineRadio111" value="option111" checked="checked">
                                            <label class="form-check-label" for="inlineRadio111">ประสงค์เบิกน้ำมัน</label>
                                        </div>
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="inlineRadio222" value="option222">
                                            <label class="form-check-label"
                                                for="inlineRadio222">ไม่ประสงค์เบิกน้ำมัน</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
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

    <script>
        $(document).ready(function() {
            $('#locationout').change(function() {
                if ($(this).val() == '2') {
                    $('#MapDistanceModal').show();
                }
            });
        });
    </script>
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
