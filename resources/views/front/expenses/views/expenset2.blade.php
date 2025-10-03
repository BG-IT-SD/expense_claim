<div id="personal-info" class="content" step="2">
    <div class="alert alert-dark mb-3 mt-3">
        <h6 class="mb-0">ส่วนที่ 2</h6>
        <small>เงื่อนไขมื้ออาหาร</small>
    </div>

    <div class="row g-4">
        @foreach ($Alldayfood as $index => $dayFood)
            @php
                $usedDateStr = $dayFood->toDateString();
                $food = $expenseFoods[$usedDateStr] ?? null;
                $isEdit = isset($expenseFoods) && $food; // ถ้ามีข้อมูลแสดงว่า edit

                // คำนวณช่วงเวลา (ใช้ตอน create)
                if ($startDate->equalTo($endDate)) {
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $endTime;
                } elseif ($dayFood->equalTo($startDate)) {
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $dayFood->copy()->setTime(23, 59);
                } elseif ($dayFood->equalTo($endDate)) {
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $endTime;
                } else {
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
                                    {{ 'วันที่: ' . $dayFood->toDateString() . ' เวลา: ' . $from->format('H:i') . ' - ' . $to->format('H:i') }}
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
                                    {{-- แถว: เบิกมื้ออาหาร --}}
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-food-outline mdi-20px text-danger me-3"></i>
                                            เบิกมื้ออาหาร
                                            <input type="hidden" name="days[{{ $index }}][date]" value="{{ $usedDateStr }}">
                                        </td>
                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
                                                $checked = '';
                                                if ($isEdit) {
                                                    // --- หน้า edit ใช้ค่าจาก DB ---
                                                    $checked = ($food && $food->{'meal'.$i} > 0) ? 'checked' : '';
                                                } else {
                                                    // --- หน้า create ใช้กติกาจากเวลา ---
                                                    // if ($i == 1 && $from->hour < 8) $checked = 'checked';             // เช้า
                                                    // if ($i == 2 && $from->hour < 17 && $to->hour > 8) $checked = 'checked'; // กลางวัน
                                                    // if ($i == 3 && $from->hour < 23 && $to->hour > 17) $checked = 'checked'; // เย็น
                                                    // if ($i == 4 && $to->hour > 21) $checked = 'checked';              // ดึก
                                                }
                                            @endphp
                                            <td>
                                                <div class="form-check form-check-inline form-check-success">
                                                    <input type="checkbox"
                                                        class="form-check-input meal-checkbox"
                                                        name="days[{{ $index }}][meal{{ $i }}][]"
                                                        data-price="{{ $groupplant->meal->{'meal'.$i} }}"
                                                        data-day="{{ $usedDateStr }}"
                                                        value="{{ $groupplant->meal->{'meal'.$i} }}"
                                                        {{ $checked }}
                                                        @if ($isView == 0) onclick="return false;" @endif>
                                                </div>
                                            </td>
                                        @endfor
                                        <td><span class="badge rounded-pill bg-label-success meal-total"></span></td>
                                    </tr>

                                    {{-- แถว: บริษัทจัดอาหารให้ --}}
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-domain mdi-20px text-info me-3"></i>
                                            บริษัทฯ จัดอาหารให้
                                        </td>
                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
                                                $reject = $food && $food->{'meal'.$i.'reject'} == 1 ? 'checked' : '';
                                            @endphp
                                            <td>
                                                <div class="form-check form-check-inline form-check-danger">
                                                    <input type="checkbox"
                                                        class="form-check-input mealx-checkbox"
                                                        name="days[{{ $index }}][mealx{{ $i }}][]"
                                                        data-price="{{ $groupplant->meal->{'meal'.$i} }}"
                                                        data-day="{{ $usedDateStr }}"
                                                        value="1"
                                                        {{ $reject }}
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

                                    {{-- แถว: รวม (แสดง DB ตอน edit) --}}
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

        {{-- รวมค่าอาหาร --}}
        <div class="row mt-3">
            <div class="col-sm-4">
                <input type="hidden" class="expense-value" name="costoffood" id="costoffood"
                    value="{{ $expense->costoffood }}">
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body alert-success row">
                        <div class="col-md-6 text-end h5">รวม</div>
                        <div class="col-md-6 text-end grandTotal h5">{{ $expense->costoffood }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ปุ่ม --}}
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
