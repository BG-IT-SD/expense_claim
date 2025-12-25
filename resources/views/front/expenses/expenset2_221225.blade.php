{{-- ================= TAB 2: ส่วนเงื่อนไขมื้ออาหาร ================= --}}
<div id="personal-info" class="content" step="2">
    <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 2</h6>
        <small>เงื่อนไขมื้ออาหาร</small>
    </div>

    <div class="row g-4">
        @foreach ($Alldayfood as $index => $dayFood)
            @php
                // คำนวณช่วงเวลาในแต่ละวัน
                $mealchecked_1 = '';
                $mealchecked_2 = '';
                $mealchecked_3 = '';
                $mealchecked_4 = '';

                $isLastDay = $dayFood->equalTo($endDate);

                if ($startDate->equalTo($endDate)) {
                    // มีแค่วันเดียว
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $endTime; // Carbon ที่รวมวันที่/เวลาแล้ว
                } elseif ($dayFood->equalTo($startDate)) {
                    // วันแรก
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $dayFood->copy()->setTime(23, 59);
                } elseif ($dayFood->equalTo($endDate)) {
                    // วันสุดท้าย
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $endTime; // ใช้เวลาเต็มจาก input
                } else {
                    // วันกลางระหว่าง
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $dayFood->copy()->setTime(23, 59);
                }

                // ---- มื้อเช้า: เริ่มก่อน 08:00 (ใช้ logic เดิม) ----
                if ($from->hour < 8 || ($to->hour > 6 && $from->hour < 8)) {
                    $mealchecked_1 = 'checked';
                }

                // ---- มื้อกลางวัน ----
                // if ($isLastDay) {
                    // ใช้สูตรใหม่เฉพาะวันสุดท้าย:
                    // ออก 08:00–12:00 และถึง/ปฏิบัติงาน 13:00–17:00
                //     $lunchStartHour = $from->hour;
                //     $lunchEndHour = $to->hour;

                //     if ($lunchStartHour >= 8 && $lunchStartHour < 12 && $lunchEndHour >= 13 && $lunchEndHour <= 17) {
                //         $mealchecked_2 = 'checked';
                //     }
                // } else {
                    // วันอื่น ๆ ใช้สูตรเดิม (เต็มวัน)
                    if ($from->hour < 17 && $to->hour > 8) {
                        $mealchecked_2 = 'checked';
                    }
                // }

                // ---- มื้อเย็น: ถ้าเดินทางกินเวลาเข้าไปเกิน 17:00 ----
                if ($from->hour < 23 && $to->hour > 17) {
                    $mealchecked_3 = 'checked';
                }

                // ---- มื้อดึก: ถ้ามีสิ้นสุดหลัง 21:00 ----
                if ($to->hour > 21) {
                    $mealchecked_4 = 'checked';
                }
            @endphp


            <div class="col-sm-12">
                <div class="card meal-day-box {{ $isLastDay ? 'is-last-day' : '' }}">
                    <div class="card-body">
                        <div class="card-header border border-info">
                            <h5>
                                <span class="badge rounded-pill bg-dark">
                                    <span class="mdi mdi-calendar-month-outline"></span>
                                    {{ 'วันที่: ' . $dayFood->toDateString() }}
                                    <span class="time-range">
                                        &nbsp;เวลา: <span class="from-time">{{ $from->format('H:i') }}</span>
                                        -
                                        <span class="to-time">{{ $to->format('H:i') }}</span>
                                    </span>
                                </span>
                            </h5>
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
                                    {{-- แถว 1: เบิกมื้ออาหาร --}}
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-food-outline mdi-20px text-danger me-3"></i>
                                            <span class="fw-medium">เบิกมื้ออาหาร</span>
                                            <input type="hidden" name="days[{{ $index }}][date]"
                                                value="{{ $dayFood->toDateString() }}">
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][meal1][]"
                                                    data-price="{{ $groupplant->meal->meal1 }}" data-meal="breakfast"
                                                    value="{{ $groupplant->meal->meal1 }}" {{ $mealchecked_1 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][meal2][]"
                                                    data-price="{{ $groupplant->meal->meal2 }}"
                                                    value="{{ $groupplant->meal->meal2 }}" {{ $mealchecked_2 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][meal3][]"
                                                    data-price="{{ $groupplant->meal->meal3 }}"
                                                    value="{{ $groupplant->meal->meal3 }}" {{ $mealchecked_3 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input class="form-check-input meal-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][meal4][]"
                                                    data-price="{{ $groupplant->meal->meal4 }}"
                                                    value="{{ $groupplant->meal->meal4 }}" {{ $mealchecked_4 }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-success me-1 meal-total"></span>
                                        </td>
                                    </tr>

                                    {{-- แถว 2: บริษัทฯ จัดอาหารให้ --}}
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-domain mdi-20px text-info me-3"></i>
                                            <span class="fw-medium">บริษัทฯ จัดอาหารให้</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][mealx1][]"
                                                    data-price="{{ $groupplant->meal->meal1 }}"
                                                    data-day="{{ $dayFood->toDateString() }}" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][mealx2][]"
                                                    data-price="{{ $groupplant->meal->meal2 }}"
                                                    data-day="{{ $dayFood->toDateString() }}" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][mealx3][]"
                                                    data-price="{{ $groupplant->meal->meal3 }}"
                                                    data-day="{{ $dayFood->toDateString() }}" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input class="form-check-input mealx-checkbox" type="checkbox"
                                                    name="days[{{ $index }}][mealx4][]"
                                                    data-price="{{ $groupplant->meal->meal4 }}"
                                                    data-day="{{ $dayFood->toDateString() }}" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-danger me-1 totalxmealcount"
                                                data-day="{{ $dayFood->toDateString() }}"></span>

                                            {{-- hidden ต่อวัน --}}
                                            <input type="hidden" name="days[{{ $index }}][totalpricebf]"
                                                class="totalpricebf" value="0">
                                            <input type="hidden" name="days[{{ $index }}][totalreject]"
                                                class="totalreject" value="0">
                                            <input type="hidden" name="days[{{ $index }}][totalprice]"
                                                class="totalprice" value="0">
                                            <input type="hidden" name="days[{{ $index }}][mealid]"
                                                value="{{ $groupplant->mealid }}">
                                        </td>
                                    </tr>

                                    {{-- แถว 3: รวม (ต่อวัน) --}}
                                    <tr class="table-info sumallday">
                                        <td><i class="mdi mdi-currency-usd mdi-20px text-info me-3"></i><span
                                                class="fw-medium">รวม</span></td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div> {{-- col --}}
        @endforeach

        {{-- แถวสรุป Grand Total --}}
        <div class="row mt-3">
            <div class="col-sm-4">
                <input type="hidden" class="expense-value" name="costoffood" id="costoffood" value="0">
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body alert-success row">
                        <div class="col-md-6 text-end h5">รวม</div>
                        <div class="col-md-6 text-end grandTotal h5">0</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ปุ่มนำทาง --}}
        <div class="col-12 d-flex justify-content-between mt-3">
            <button class="btn btn-outline-secondary btn-prev waves-effect">
                <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            <button class="btn btn-primary btn-next waves-effect waves-light">
                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                <i class="mdi mdi-arrow-right"></i>
            </button>
        </div>
    </div> {{-- row --}}
</div>
{{-- ================= /TAB 2 ================= --}}
