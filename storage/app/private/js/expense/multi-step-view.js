(function () {
    const wizardNumbered = document.querySelector('.wizard-numbered'),
    wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next')),
    wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev')),
    wizardNumberedBtnSubmit = wizardNumbered.querySelector('.btn-submit');

  if (typeof wizardNumbered !== undefined && wizardNumbered !== null) {
    const numberedStepper = new Stepper(wizardNumbered, {
      linear: false
    });
    if (wizardNumberedBtnNextList) {
      wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
        wizardNumberedBtnNext.addEventListener('click', event => {
          numberedStepper.next();
        });
      });
    }
    if (wizardNumberedBtnPrevList) {
      wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
        wizardNumberedBtnPrev.addEventListener('click', event => {
          numberedStepper.previous();
        });
      });
    }
if (wizardNumberedBtnSubmit) {
    wizardNumberedBtnSubmit.addEventListener('click', event => {
        event.preventDefault();

        // กันกดซ้ำ
        const $btn = $(wizardNumberedBtnSubmit).prop('disabled', true);

        Swal.fire({
        title: 'กำลังบันทึกข้อมูล...',
        text: 'โปรดรอสักครู่',
        allowOutsideClick: false,
        showConfirmButton: false,
        showCancelButton: false,
        didOpen: () => Swal.showLoading()
        });

        const form = $('#expensefrmview')[0];
        const formData = new FormData(form);
        const id = $('#expense_id').val();
        formData.append('_method', 'PUT');

        $.ajax({
        url: '/HR/' + id,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            Swal.close();

            // เผื่อ API ตอบ 204/empty ให้มีข้อความเริ่มต้น
            const msg = response?.message || 'บันทึกสำเร็จ';
            const cls = response?.class || 'success';

            Swal.fire({
            title: msg,
            icon: cls,
            customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
            buttonsStyling: false,
            }).then(result => {
            if (result.isConfirmed) {
                const pageMode = parseInt($('#page_mode').val() || '0', 10);

                if (pageMode === 1) {
                // ใช้ replace กันผู้ใช้กดย้อนแล้วเจอฟอร์มส่งซ้ำ
                window.location.replace('/HR');
                } else if (pageMode === 3) {
                // พยายามกลับหน้าก่อนหน้าถ้าเป็นโดเมนเดียวกัน
                const ref = document.referrer || '';
                if (ref && new URL(ref, window.location.origin).origin === window.location.origin) {
                    window.location.href = ref;
                } else if (window.history.length > 1) {
                    window.history.back();
                } else {
                    // fallback
                    window.location.replace('/HR');
                }
                } else {
                window.location.replace('/HR');
                }
            }
            });
        },
        error: function (xhr) {
            Swal.close();
            Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: xhr.responseJSON?.message || 'ไม่สามารถบันทึกข้อมูลได้',
            });
        },
        complete: function () {
            // เปิดปุ่มกลับ
            $btn.prop('disabled', false);
        }
        });
    });
}



  }
}) ();
