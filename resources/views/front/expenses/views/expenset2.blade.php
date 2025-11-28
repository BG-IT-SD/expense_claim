{{-- ================= TAB 2: ส่วนเงื่อนไขมื้ออาหาร (VIEW) ================= --}}
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

                // คำนวณช่วงเวลาในแต่ละวัน (เหมือนหน้า create)
                if ($startDate->equalTo($endDate)) {
                    // มีแค่วันเดียว
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $endTime; // Carbon
                } elseif ($dayFood->equalTo($startDate)) {
                    // วันแรก
                    $from = $dayFood->copy()->setTimeFromTimeString($startTime);
                    $to = $dayFood->copy()->setTime(23, 59);
                } elseif ($dayFood->equalTo($endDate)) {
                    // วันสุดท้าย
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $endTime;
                } else {
                    // วันกลาง
                    $from = $dayFood->copy()->setTime(6, 0);
                    $to = $dayFood->copy()->setTime(23, 59);
                }

                $isLastDay = $dayFood->equalTo($endDate);
            @endphp

            <div class="col-sm-12">
                <div class="card meal-day-box {{ $isLastDay ? 'is-last-day' : '' }}"
                    data-from="{{ $from->format('H:i') }}" data-to="{{ $to->format('H:i') }}">
                    <div class="card-body">
                        <div class="card-header border border-info">
                            <h5>
                                <span class="badge rounded-pill bg-dark">
                                    <span class="mdi mdi-calendar-month-outline"></span>
                                    {{ 'วันที่: ' . $usedDateStr }}
                                    <span class="time-range">
                                        &nbsp;เวลา:
                                        <span class="from-time">{{ $from->format('H:i') }}</span>
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
                                    {{-- แถว 1: เบิกมื้ออาหาร (ใช้ค่าจาก DB) --}}
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-food-outline mdi-20px text-danger me-3"></i>
                                            <span class="fw-medium">เบิกมื้ออาหาร</span>
                                            <input type="hidden" name="days[{{ $index }}][date]"
                                                value="{{ $usedDateStr }}">
                                        </td>

                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
                                                // ถ้ามีค่า > 0 แปลว่าเบิกมื้อนั้น
                                                $checked = $food && $food->{'meal' . $i} > 0 ? 'checked' : '';
                                            @endphp
                                            <td>
                                                <div class="form-check form-check-inline form-check-success">
                                                    <input class="form-check-input meal-checkbox" type="checkbox"
                                                        name="days[{{ $index }}][meal{{ $i }}][]"
                                                        data-price="{{ $groupplant->meal->{'meal' . $i} }}"
                                                        data-day="{{ $usedDateStr }}"
                                                        value="{{ $groupplant->meal->{'meal' . $i} }}"
                                                        {{ $checked }}
                                                        @if ($isView == 0) onclick="return false;" @endif>
                                                </div>
                                            </td>
                                        @endfor

                                        <td>
                                            <span class="badge rounded-pill bg-label-success me-1 meal-total"
                                                data-day="{{ $usedDateStr }}">
                                                {{ number_format($food->totalpricebf ?? ($food->totalprice ?? 0), 2) }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- แถว 2: บริษัทฯ จัดอาหารให้ (ใช้ค่า reject จาก DB) --}}
                                    <tr>
                                        <td>
                                            <i class="mdi mdi-domain mdi-20px text-info me-3"></i>
                                            <span class="fw-medium">บริษัทฯ จัดอาหารให้</span>
                                        </td>

                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
                                                $reject =
                                                    $food && $food->{'meal' . $i . 'reject'} == 1 ? 'checked' : '';
                                            @endphp
                                            <td>
                                                <div class="form-check form-check-inline form-check-danger">
                                                    <input class="form-check-input mealx-checkbox" type="checkbox"
                                                        name="days[{{ $index }}][mealx{{ $i }}][]"
                                                        data-price="{{ $groupplant->meal->{'meal' . $i} }}"
                                                        data-day="{{ $usedDateStr }}" value="1"
                                                        {{ $reject }}
                                                        @if ($isView == 0) onclick="return false;" @endif>


                                                </div>
                                            </td>
                                        @endfor

                                        <td>
                                            <span class="badge rounded-pill bg-label-danger me-1 totalxmealcount"
                                                data-day="{{ $usedDateStr }}">
                                                {{ number_format($food->totalreject ?? 0, 2) }}
                                            </span>

                                            {{-- hidden ต่อวัน (ถ้าหน้านี้ submit ต่อก็เก็บค่าเดิมจาก DB) --}}
                                            <input type="hidden" name="days[{{ $index }}][totalpricebf]"
                                                class="totalpricebf" value="{{ $food->totalpricebf ?? 0 }}">
                                            <input type="hidden" name="days[{{ $index }}][totalreject]"
                                                class="totalreject" value="{{ $food->totalreject ?? 0 }}">
                                            <input type="hidden" name="days[{{ $index }}][totalprice]"
                                                class="totalprice" value="{{ $food->totalprice ?? 0 }}">
                                            <input type="hidden" name="days[{{ $index }}][mealid]"
                                                value="{{ $food->mealid ?? $groupplant->mealid }}">
                                        </td>
                                    </tr>

                                    {{-- แถว 3: รวม (ต่อวัน) ใช้ตัวเลขจาก DB --}}
                                    <tr class="table-info sumallday">
                                        <td>
                                            <i class="mdi mdi-currency-usd mdi-20px text-info me-3"></i>
                                            <span class="fw-medium">รวม</span>
                                        </td>
                                        <td>{{ number_format($food->meal1 ?? 0, 2) }}</td>
                                        <td>{{ number_format($food->meal2 ?? 0, 2) }}</td>
                                        <td>{{ number_format($food->meal3 ?? 0, 2) }}</td>
                                        <td>{{ number_format($food->meal4 ?? 0, 2) }}</td>
                                        <td>{{ number_format($food->totalprice ?? 0, 2) }}</td>
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
                <input type="hidden" class="expense-value" name="costoffood" id="costoffood"
                    value="{{ $expense->costoffood }}">
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body alert-success row">
                        <div class="col-md-6 text-end h5">รวม</div>
                        <div class="col-md-6 text-end grandTotal h5">
                            {{ number_format($expense->costoffood, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ปุ่มนำทาง (ถ้าหน้านี้เป็นแค่ view จะเอาออกหรือ disable ก็ได้) --}}
        <div class="col-12 d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-outline-secondary btn-prev waves-effect">
                <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            <button type="button" class="btn btn-primary btn-next waves-effect waves-light">
                <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                <i class="mdi mdi-arrow-right"></i>
            </button>
        </div>
    </div> {{-- row --}}
</div>
{{-- ================= /TAB 2 (VIEW) ================= --}}
