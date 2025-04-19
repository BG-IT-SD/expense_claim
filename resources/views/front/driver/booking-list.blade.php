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
                                    <td>{{ $booking->departure_date . ' : ' . $booking->departure_time.' - '.$booking->return_date . ' : ' . $booking->return_time }}</td>
                                    <td>{{ $booking->location_name }}</td>
                                    <td>{{ $booking->type_travel }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="div mt-3 mb-3 text-center">
                        <button type="submit" class="btn btn-primary mt-2" id="claimBtn">เบิกรายการที่เลือก</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.booking-checkbox').on('change', function () {
            if ($('.booking-checkbox:checked').length > 7) {
                $(this).prop('checked', false);
                alert('เลือกได้ไม่เกิน 7 รายการเท่านั้น');
            }
        });

        // ถ้าอยากเลือกทั้งหมด
        $('#selectAll').on('change', function () {
            if ($(this).is(':checked')) {
                $('.booking-checkbox').each(function (index) {
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

        // กดส่งฟอร์ม
        // $('#bookingForm').on('submit', function (e) {
        //     e.preventDefault();

        //     const selected = $('.booking-checkbox:checked').map(function () {
        //         return $(this).val();
        //     }).get();

        //     if (selected.length === 0) {
        //         alert('กรุณาเลือกรายการอย่างน้อย 1 รายการ');
        //         return;
        //     }

        //     // ✅ ส่งไปเบิก
        //     console.log('รายการที่เลือก:', selected);
        //     // ตัวอย่างส่งด้วย AJAX หรือ redirect ได้ตามต้องการ
        //     // $.post('/driver/claim', { bookings: selected }, ... );
        // });
    });
    </script>



