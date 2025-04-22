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
                    class="form-control" value="{{ $expense->publictransportfare }}">
                <label for="publictransportfare">รถโดยสารสาธารณะทั่วไป / บาท</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="expresswaytoll" name="expresswaytoll" min="0" class="form-control"
                    value="{{ $expense->expresswaytoll }}">
                <label for="expresswaytoll">ค่าทางด่วน / บาท</label>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-floating form-floating-outline">
                <input type="number" id="otherexpenses" name="otherexpenses" min="0" class="form-control"
                    value="{{ $expense->otherexpenses }}">
                <label for="otherexpenses">ค่าใช้จ่ายอื่นๆ / บาท</label>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card ">
                <h5 class="card-header">ไฟล์หลักฐาน</h5>
                <div class="card-body row">


                    @if ($files && $files->count() > 0)
                        <ul class="list-group">
                            @foreach ($files as $key => $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ asset('storage/' . $file->path) }}" target="_blank">
                                        <i class="mdi mdi-file-document-outline text-primary"></i>
                                        {{ 'file_' . ($key + 1) }}
                                    </a>
                                    <span
                                        class="badge bg-info">{{ strtoupper(pathinfo($file->etc, PATHINFO_EXTENSION)) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">ไม่มีไฟล์แนบ</p>
                    @endif


                    {{-- <div class="col-md-8">
                        <div id="file-container">
                            <div class="file-row mb-2 d-flex gap-2 align-items-center">
                                <input type="file" name="files[]" class="form-control w-75">
                                <button type="button" class="btn btn-danger btn-remove">ลบ</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4"><button type="button" id="add-file"
                            class="btn btn-secondary mb-3">เพิ่มไฟล์</button>
                    </div> --}}



                </div>
            </div>
        </div>
    </div>
    @if ($expense->vbooking->type_reserve == 4)
        {{-- น้ำมัน --}}
        <div class="alert alert-dark mb-3 mt-3">
            <h6 class="mb-0">ส่วนที่ 3</h6>
            <small>ค่าน้ำมัน</small>
        </div>
        <div class="row g-4">
            <div class="col-sm-12 text-center">
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="checktoil" id="checktoil_1"
                        value="1" @if ($expense->checktoil == 1)
                            checked="checked"
                        @endif>
                    <label class="form-check-label" for="checktoil_1">ประสงค์เบิกน้ำมัน</label>
                </div>
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="checktoil" id="checktoil_2"
                        value="2" @if ($expense->checktoil == 2)
                        checked="checked"
                    @endif>
                    <label class="form-check-label" for="checktoil_2">ไม่ประสงค์เบิกน้ำมัน</label>
                </div>
            </div>
            <div class="col-sm-12" @if ($expense->checktoil == 1) @else style="display: none;" @endif>
                <div class="card">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="card-header">Rate อัตราค่าน้ำมัน</h5>
                        </div>
                        <div class="col-sm-6 pt-3">

                            <h5><span class="badge rounded-pill bg-label-danger me-1">วันที่การเดินทาง :
                                    {{ $departure_date }}
                                </span>
                            </h5>
                            <h5>
                                <span class="badge rounded-pill bg-label-primary me-1">ราคาน้ำมัน ณ วันนั้น :
                                    {{ $expense->fuel->price ?? '' }}
                                </span>
                                    <input type="hidden" name="fuel91id" value="{{ $expense->fuelpricesid ?? '' }}">
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
                                                        {{ isset($expense->fuelpricesid) && $ratefuel->id == $expense->fuelpricesid ? 'checked' : '' }}
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
            <div class="col-sm-12" @if ($expense->checktoil == 1) @else style="display: none;" @endif>
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
                            <td> <h5><span class="badge rounded-pill bg-danger lastkm"> {{ $expense->totaldistance }}</span></h5></td>
                            <td>
                                <h5><span class="badge rounded-pill bg-danger">{{ $expense->fuelprice->bathperkm ?? "" }} บาท</span></h5>
                                <input type="hidden" id="bath_per_km" name="bath_per_km" value="{{ $expense->fuelprice->bathperkm ?? "" }}">
                                <input type="hidden" id="fuelpricesid" name="fuelpricesid"
                                    value="{{ $expense->fuelpricesid }}"></td>
                            <td><h5><span class="badge rounded-pill bg-success pricesuccess">{{ $expense->gasolinecost }} </span></h5></td>
                        </tr>
                    </tbody>
                </table>
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
                        <td><span
                                class="btn rounded-pill btn-primary waves-effect waves-light totallastfood">{{ $expense->costoffood }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>ค่าเดินทาง และ อื่นๆ</td>
                        <td><span
                                class="btn rounded-pill btn-primary waves-effect waves-light totaltravel">{{ $expense->travelexpenses }}</span>
                            <input type="hidden" class="expense-value" id="travelexpenses" name="travelexpenses"
                                value="{{ $expense->travelexpenses }}">
                        </td>
                    </tr>
                    <tr>
                        <td>ค่าน้ำมัน</td>
                        <td><span
                                class="btn rounded-pill btn-primary waves-effect waves-light gasolinecost">{{ $expense->gasolinecost }}</span>
                            <input type="hidden" class="expense-value" id="gasolinecost" name="gasolinecost"
                                value="{{ $expense->gasolinecost }}">
                        </td>
                    </tr>
                    <tr>
                        <td>รวม</td>
                        <td><span
                                class="btn rounded-pill btn-success waves-effect waves-light totalExpense">{{ $expense->totalprice }}</span>
                            <input type="hidden" id="totalExpense" name="totalExpense"
                                value="{{ $expense->totalprice }}">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- Apporve Timeline --}}
        <div class="row g-4">
            <div class="col-sm-12">
                <div class="card shadow-none bg-transparent border border-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title "><span
                                class="badge rounded-pill bg-primary">สถานะลำดับการอนุมัติ</span>
                        </h5>
                        {{-- <hr> --}}
                        <div class="timeline-horizontal">
                            @foreach ($approvals as $index => $item)
                                <div class="timeline-step">
                                    <div class="circle">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="label">{!! type_approve_text($item->typeapprove) !!}</div>
                                    <div class="approver">{{ $item->approvename }}</div>
                                    <div class="status-badge">
                                        @if ($item->statusapprove == 1)
                                            <span class="badge bg-success">อนุมัติแล้ว</span>
                                        @elseif ($item->statusapprove == 2)
                                            <span class="badge bg-danger">ไม่อนุมัติ</span>
                                        @else
                                            <span class="badge bg-warning text-dark">รออนุมัติ</span>
                                        @endif
                                    </div>
                                    <div class="timestamp">
                                        {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>


                    </div>
                </div>
            </div>
        </div>
        {{-- End Apporve Timeline --}}
        @if ($isView == 1)
            {{-- Apporve for Head --}}
            <div class="row g-4">
                <div class="col-sm-6">
                    <div class="card shadow-none bg-transparent border border-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title "><span class="badge rounded-pill bg-primary">ผู้ตรวจสอบ</span>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4><span class="badge bg-label-dark h4"><span
                                                class="mdi mdi-account-check h4"></span>
                                            {{ $finalHName . ' | ' . $finalHEmail }}</span></h4>
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="head_id" id="head_id" value="{{ $finalId }}"
                                        class="form-control form-control-input">
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="head_email" id="head_email"
                                        value="{{ $finalHEmail }}" class="form-control form-control-input">
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="head_name" id="head_name"
                                        value="{{ $finalHName }}" class="form-control form-control-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Next Step --}}

                <div class="col-sm-6">
                    <div class="card shadow-none bg-transparent border border-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title "><span
                                    class="badge rounded-pill bg-primary">ลำดับอนุมัติถัดไป</span>
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4><span class="badge bg-label-dark h4"><span
                                                class="mdi mdi-account-switch h4"></span>
                                            {{ $finalHNameNext . ' | ' . $finalHEmailNext }}</span></h4>
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="nexthead_id" id="nexthead_id"
                                        value="{{ $finalIdNext }}" class="form-control form-control-input" readonly>
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="nexthead_email" id="nexthead_email"
                                        value="{{ $finalHEmailNext }}" class="form-control form-control-input">
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="nexthead_name" id="nexthead_name"
                                        value="{{ $finalHNameNext }}" class="form-control form-control-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- End Apporve for Head --}}
        <div class="col-12 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary btn-prev waves-effect">
                <i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            @if ($isView == 1)
                <div class="demo-inline-spacing">
                    <button type="button" id="rejectbtn" class="btn btn-danger waves-effect waves-light"><span
                            class="mdi mdi-file-cancel"></span> ไม่ผ่านการตรวจสอบ</button>
                    <button class="btn btn-primary btn-submit waves-effect waves-light"><span
                            class="mdi mdi-content-save-check"></span> ยืนยันตรวจสอบข้อมูล</button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Reject --}}
@if ($isView == 1)
<div class="modal fade" id="popUpReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <form action="" id="rejectfrm" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <label for="rejectremark">
                                <h4>เหตุผลที่ยกเลิก</h4>
                            </label>
                            <textarea name="rejectremark" id="rejectremark" class="form-control" cols="30" rows="10"></textarea>
                            <input type="hidden" id="rejectidexpense" name="rejectidexpense"
                                value="{{ $expense->id }}">
                            <input type="hidden" name="head_emailrj" id="head_emailrj" value="{{ $finalHEmail }}"
                                class="form-control form-control-input">
                            <input type="hidden" name="head_namerj" id="head_namerj" value="{{ $finalHName }}"
                                class="form-control form-control-input">
                            <input type="hidden" name="head_idrj" id="head_idrj" value="{{ $finalId }}"
                                class="form-control form-control-input">
                                <input type="hidden" name="departuredaterj" id="departuredaterj" value="{{ $departure_date.' - '. $return_date}}"
                                class="form-control form-control-input">
                                <input type="hidden" name="empemailrj" id="empemailrj" value="{{ $expense->user->email }}"
                                class="form-control form-control-input">
                                <input type="hidden" name="empfullname" id="empfullname" value="{{ $expense->user->fullname }}"
                                class="form-control form-control-input">

                        </div>
                        <hr>
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-primary btnreject">ยืนยัน</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
