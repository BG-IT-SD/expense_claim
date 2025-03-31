<div id="personal-info" class="content">
    {{-- <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 2</h6>
        <small>รายชื่อพนักงาน</small>
    </div> --}}
    {{-- <div class="row g-4">
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
    </div> --}}
    <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 2</h6>
        <small>เงื่อนไขมื้ออาหาร</small>
    </div>
    <div class="row g-4">
        @foreach ($Alldayfood as $dayFood)
            {{-- {{ $dayFood->format('Y-m-d') . "\n"; }} --}}
            @php
                $mealchecked_1 = '';
                $mealchecked_2 = '';
                $mealchecked_3 = '';
                $mealchecked_4 = '';
                if ($startDate->equalTo($endDate)) {
                    // มีแค่วันเดียว
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $endTime; // ใช้ $endDate ที่รวมเวลาไว้แล้ว
                } elseif ($dayFood->equalTo($startDate)) {
                    // วันแรก
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $dayFood->copy()->setTime(23, 59);
                } elseif ($dayFood->equalTo($endDate)) {
                    // วันสุดท้าย
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $endTime; // ใช้เวลาแบบเต็ม
                } else {
                    // วันที่อยู่ระหว่าง
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $dayFood->copy()->setTime(23, 59);
                }

                if ($from->hour < 8 || ($to->hour > 6 && $from->hour <= 8)) {
                    $mealchecked_1 = 'checked';
                }

                if ($from->hour < 17 && $to->hour >= 8) {
                    $mealchecked_2 = 'checked';
                }

                if ($from->hour < 23 && $to->hour >= 17) {
                    $mealchecked_3 = 'checked';
                }

                if ($to->hour >= 21) {
                    $mealchecked_4 = 'checked';
                }
            @endphp
            <div class="col-sm-12">
                <div class="card meal-day-box">

                    <div class="card-body">
                        <div class="card-header border border-info">
                            <h5><span class="badge rounded-pill bg-dark"><span
                                        class="mdi mdi-calendar-month-outline"></span>
                                    {{ 'วันที่: ' . $dayFood->toDateString() . ' เวลา: ' . $from->format('H:i') . ' - ' . $to->format('H:i') . "\n" }}
                                </span></h5>
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
                                            <i class="mdi mdi-food-outline mdi-20px text-danger me-3"></i><span
                                                class="fw-medium">เบิกมื้ออาหาร</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox" name="meal1[]" data-price="{{ $groupplant->meal->meal1 }}" data-meal="breakfast"
                                                    value="{{ $groupplant->meal->meal1 }}" {{ $mealchecked_1 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox" name="meal2[]" data-price="{{ $groupplant->meal->meal2 }}"
                                                    value="{{ $groupplant->meal->meal2 }}" {{ $mealchecked_2 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox" name="meal3[]" data-price="{{ $groupplant->meal->meal3 }}"
                                                    value="{{ $groupplant->meal->meal3 }}" {{ $mealchecked_3 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox" name="meal4[]" data-price="{{ $groupplant->meal->meal4 }}"
                                                    value="{{ $groupplant->meal->meal4 }}" {{ $mealchecked_4 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-success me-1 meal-total"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-domain mdi-20px text-info me-3"></i><span
                                                class="fw-medium">บริษัทฯ จัดอาหารให้</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox" name="mealx1[]" data-price="{{ $groupplant->meal->meal1 }}" data-day="{{ $dayFood->toDateString() }}"
                                                    value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox" name="mealx2[]" data-price="{{ $groupplant->meal->meal2 }}" data-day="{{ $dayFood->toDateString() }}"
                                                    value="1">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox" name="mealx3[]" data-price="{{ $groupplant->meal->meal3 }}" data-day="{{ $dayFood->toDateString() }}"
                                                    value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox" name="mealx4[]" data-price="{{ $groupplant->meal->meal4 }}" data-day="{{ $dayFood->toDateString() }}"
                                                    value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-danger me-1 totalxmealcount" data-day="{{ $dayFood->toDateString() }}"></span>
                                        </td>
                                    </tr>
                                    <tr class="table-info sumallday">
                                        <td>
                                            <i class="mdi mdi-currency-usd mdi-20px text-info me-3"></i><span
                                                class="fw-medium">รวม</span>
                                        </td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
        <div class="col-md-12">
            <div class="card">
                <div class="card-body alert-success row">
                    <div class="col-md-6 text-end">รวม</div>
                    <div class="col-md-6 text-end">0
                        <input type="hidden" name="totalpricebf" value="0">
                        <input type="hidden" name="totalreject" value="0">
                        <input type="hidden" name="totalprice" value="0">
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
