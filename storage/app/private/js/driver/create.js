function updateMealTotal() {
    let grandTotal = 0;
    $('.meal-day-box').each(function() {
        let rowTotal = 0;
        $(this).find('.meal-checkbox').each(function() {
            if ($(this).is(':checked')) {
                rowTotal += parseFloat($(this).data('price')) || 0;
            }
        });
        $(this).find('.meal-total').text(rowTotal.toFixed(2));
        grandTotal += rowTotal;
    });
    $('.grandTotal').text(grandTotal.toFixed(2));
    $('#costoffood').val(grandTotal.toFixed(2));
}

// call on load
$(document).ready(function() {
    updateMealTotal();
    $('#btnConfirmClaim').on('click', function() {
        Swal.fire({
            title: 'ยืนยันการเบิก?',
            text: 'คุณแน่ใจว่าต้องการบันทึกข้อมูลการเบิกรายการอาหาร?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $('form').submit();
            }
        });
    });
});

// bind event
$(document).on('change', '.meal-checkbox', updateMealTotal);