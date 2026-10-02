$(document).ready(function() {
    $('#drivers').select2();
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();

        const empid = $('#drivers').val();
        if (!empid) return;

        $.get('/DriverClaim/search-booking', {
            empid
        }, function(res) {
            $('#resultArea').html(res); // แสดงผล HTML ที่ได้จาก controller
        }).fail(function() {
            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
        });
    });

});