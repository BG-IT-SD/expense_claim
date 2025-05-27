<div id="personal-info" class="content" step="2">
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
        @foreach ($Alldayfood as $index => $dayFood)
        @php
            $usedDateStr = $dayFood->toDateString();
            $food = $expenseFoods[$usedDateStr] ?? null;

            $mealchecked_1 = $food && $food->meal1 > 0 ? 'checked' : '';
            $mealchecked_2 = $food && $food->meal2 > 0 ? 'checked' : '';
            $mealchecked_3 = $food && $food->meal3 > 0 ? 'checked' : '';
            $mealchecked_4 = $food && $food->meal4 > 0 ? 'checked' : '';

            $reject_1 = $food && $food->meal1reject == 1 ? 'checked' : '';
            $reject_2 = $food && $food->meal2reject == 1 ? 'checked' : '';
            $reject_3 = $food && $food->meal3reject == 1 ? 'checked' : '';
            $reject_4 = $food && $food->meal4reject == 1 ? 'checked' : '';

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



      @endphp

        <div class="col-sm-12">
            <div class="card meal-day-box">
                <div class="card-body">
                    <div class="card-header border border-info">
                        <h5>
                            <span class="badge rounded-pill bg-dark">
                                <span class="mdi mdi-calendar-month-outline"></span>
                                {{ 'วันที่: ' . $dayFood->toDateString() . ' เวลา: ' . $from->format('H:i') . ' - ' . $to->format('H:i') . "\n" }}
                            </span>
                        </h5>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr class="table-info">
                                    <th>รายละเอียด</th>
                                    <th>มื้อเช้า</th>
                                    <th>มื้อกลางวัน</th>
                                    <th>มื้อเย็น</th>
                                    <th>มื้อดึก</th>
                                    <th>รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="mdi mdi-food-outline mdi-20px text-danger me-3"></i>
                                        เบิกมื้ออาหาร
                                        <input type="hidden" name="days[{{ $index }}][date]" value="{{ $usedDateStr }}">
                                    </td>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <td>
                                            <div class="form-check form-check-inline form-check-success">
                                                <input type="checkbox"
                                                    class="form-check-input meal-checkbox"
                                                    name="days[{{ $index }}][meal{{ $i }}][]"
                                                    data-price="{{ $groupplant->meal->{'meal'.$i} }}"
                                                    value="{{ $groupplant->meal->{'meal'.$i} }}"
                                                    {{ ${'mealchecked_'.$i} }}
                                                    onclick="return false;">
                                            </div>
                                        </td>
                                    @endfor
                                    <td><span class="badge rounded-pill bg-label-success meal-total"></span></td>
                                </tr>

                                <tr>
                                    <td>
                                        <i class="mdi mdi-domain mdi-20px text-info me-3"></i>
                                        บริษัทฯ จัดอาหารให้
                                    </td>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <td>
                                            <div class="form-check form-check-inline form-check-danger">
                                                <input type="checkbox"
                                                    class="form-check-input mealx-checkbox"
                                                    name="days[{{ $index }}][mealx{{ $i }}][]"
                                                    data-price="{{ $groupplant->meal->{'meal'.$i} }}"
                                                    data-day="{{ $usedDateStr }}"
                                                    value="1"
                                                    {{ ${'reject_'.$i} }}
                                                    @if ($isView == 0) onclick="return false;" @endif>
                                            </div>
                                        </td>
                                    @endfor
                                    <td>
                                        <span class="badge rounded-pill bg-label-danger totalxmealcount"
                                            data-day="{{ $usedDateStr }}"></span>
                                        <input type="hidden" name="days[{{ $index }}][totalpricebf]" class="totalpricebf"
                                            value="{{ $food->totalpricebf ?? 0 }}">
                                        <input type="hidden" name="days[{{ $index }}][totalreject]" class="totalreject"
                                            value="{{ $food->totalreject ?? 0 }}">
                                        <input type="hidden" name="days[{{ $index }}][totalprice]" class="totalprice"
                                            value="{{ $food->totalprice ?? 0 }}">
                                        <input type="hidden" name="days[{{ $index }}][mealid]"
                                            value="{{ $food->mealid ?? $groupplant->mealid }}">
                                    </td>
                                </tr>

                                <tr class="table-info sumallday">
                                    <td><i class="mdi mdi-currency-usd mdi-20px text-info me-3"></i>รวม</td>
                                    <td>{{ $food->meal1 ?? 0 }}</td>
                                    <td>{{ $food->meal2 ?? 0 }}</td>
                                    <td>{{ $food->meal3 ?? 0 }}</td>
                                    <td>{{ $food->meal4 ?? 0 }}</td>
                                    <td>{{ $food->totalprice ?? 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
        <div class="row mt-3">
            <div class="col-sm-4">
                <input type="hidden"  class="expense-value" name="costoffood"  id="costoffood" value="{{ $expense->costoffood }}">

            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body alert-success row">
                        <div class="col-md-6 text-end h5">รวม</div>
                        <div class="col-md-6 text-end grandTotal h5">{{ $expense->costoffood }}

                        </div>
                    </div>
                </div>
            </div>

        </div>



        <div class="col-12 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary btn-prev waves-effect">
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
