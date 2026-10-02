$ (document).ready (function () {
  $ ('#ExpenseList').DataTable ({
    processing: true,
    order: [[2, 'desc']],
    lengthMenu: [5, 10, 25, 50, 75, 100],
  });

  $ ('#exdate').flatpickr ({
    monthSelectorType: 'static',
  });

  $ ('#end_exdate').flatpickr ({
    monthSelectorType: 'static',
  });


});

function cancelExpense(expenseId) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'ต้องการยกเลิกรายการเบิกนี้หรือไม่',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ยกเลิก!',
        cancelButtonText: 'ไม่ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/Expense/' + expenseId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $ ('meta[name="csrf-token"]').attr ('content'),
                },
                success: function (response) {
                    if (response.status === 'success') {
                        Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('ไม่สำเร็จ', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}
