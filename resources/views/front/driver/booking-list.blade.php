<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <h5 class="card-header"><i class="mdi mdi-view-list"></i> รายการ Booking</h5>
            <div class="table-responsive text-nowrap">
                <form id="bookingForm" action="{{ route('DriverClaim.create') }}" method="POST">
                    @csrf
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" /></th>
                                <th>BOOKING ID</th>
                                <th>วันที่เดินทาง</th>
                                <th>สถานที่</th>
                                <th>ประเภท</th>
                                {{-- <th>แก้เวลา</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="booking_ids[]" class="booking-checkbox"
                                            value="{{ $booking->id }}">
                                    </td>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->departure_date . ' : ' . $booking->departure_time . ' - ' . $booking->return_date . ' : ' . $booking->return_time }}
                                        <button type="button" class="btn btn-sm btn-outline-primary btnEditBookingTime"
                                            data-bookid="{{ $booking->id }}" data-date="{{ $booking->departure_date }}"
                                            data-start="{{ \Carbon\Carbon::parse($booking->departure_time)->format('H:i') }}"
                                            data-end="{{ \Carbon\Carbon::parse($booking->return_time)->format('H:i') }}"
                                            data-location="{{ $booking->location_name }}"
                                            data-action="{{ route('DriverClaim.CarBooking.updateTime', ['bookid' => $booking->id]) }}">
                                            <span class="mdi mdi-clock-edit-outline"></span> แก้เวลา
                                        </button>
                                    </td>
                                    <td>{{ $booking->location_name }}</td>
                                    <td>{{ $booking->type_travel }}</td>
                                    {{-- <td class="text-center">

                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (count($bookings) > 0)
                        <div class="div mt-3 mb-3 text-center">
                            <button type="submit" class="btn btn-primary mt-2"
                                id="claimBtn">เบิกรายการที่เลือก</button>
                        </div>
                    @endif

                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditBookingTime" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditBookingTime" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">
                    แก้ไขเวลา Booking: <span id="mBookId"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2 text-muted small">
                    วันที่: <span id="mDate"></span> | สถานที่: <span id="mLocation"></span>
                </div>

                <input type="hidden" name="bookid" id="inputBookId">

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">เวลาเริ่ม (24 ชม)</label>
                        <input type="text" class="form-control" name="start_time" id="inputStart" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">เวลาสิ้นสุด (24 ชม)</label>
                        <input type="text" class="form-control" name="end_time" id="inputEnd" required>
                    </div>
                </div>

                <div class="form-text mt-2">รูปแบบเวลา: 00:00 – 23:59</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary">
                    <span class="mdi mdi-content-save-outline"></span> บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.booking-checkbox').on('change', function() {
            if ($('.booking-checkbox:checked').length > 7) {
                $(this).prop('checked', false);
                alert('เลือกได้ไม่เกิน 7 รายการเท่านั้น');
            }
        });

        // ถ้าอยากเลือกทั้งหมด
        $('#selectAll').on('change', function() {
            if ($(this).is(':checked')) {
                $('.booking-checkbox').each(function(index) {
                    if (index < 7) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
            } else {
                $('.booking-checkbox').prop('checked', false);
            }
        });

    });
</script>
