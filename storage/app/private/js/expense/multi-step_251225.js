(function () {
  const wizardNumbered = document.querySelector('.wizard-numbered'),
        wizardNumberedBtnNextList = wizardNumbered ? [].slice.call(wizardNumbered.querySelectorAll('.btn-next')) : [],
        wizardNumberedBtnPrevList = wizardNumbered ? [].slice.call(wizardNumbered.querySelectorAll('.btn-prev')) : [],
        wizardNumberedBtnSubmit = wizardNumbered ? wizardNumbered.querySelector('.btn-submit') : null;

  if (wizardNumbered) {
    const numberedStepper = new Stepper(wizardNumbered, { linear: false });

    const fv = FormValidation.formValidation(document.getElementById('expensefrm'), {
      fields: {
        returntime: {
          validators: {
            notEmpty: { message: 'กรุณากรอกเวลา' },
            regexp: {
              regexp: /^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
              message: 'รูปแบบเวลาต้องเป็น HH:MM:SS เช่น 20:00:00',
            },
          },
        },
        headapprove: {
          validators: { notEmpty: { message: 'กรุณาเลือกผู้อนุมัติ' } },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5(),
        submitButton: new FormValidation.plugins.SubmitButton(),
      },
    });

    // ✅ บังคับกด "คำนวณระยะทาง" ถ้ามีแผนที่และเลือกต้นทางปลายทางแล้ว
    wizardNumberedBtnNextList.forEach(btn => {
      btn.addEventListener('click', event => {
        event.preventDefault();
        const currentStep = btn.closest('.content[step]');
        if (!currentStep) return;

        fv.validate().then(status => {
          if (status === 'Valid') {
            const mapEl = document.getElementById('map');
            const hasMap = mapEl && currentStep.contains(mapEl);

            if (hasMap) {
              const origin = document.getElementById('origin')?.value.trim();
              const destination = document.getElementById('destination')?.value.trim();

              if (origin && destination && !distanceCalculated) {
                Swal.fire({
                  icon: 'warning',
                  title: 'ยังไม่ได้คำนวณระยะทาง',
                  text: 'กรุณากดปุ่ม "คำนวณระยะทาง" ก่อนดำเนินการต่อ',
                });
                return;
              }
            }

            numberedStepper.next();
          }
        });
      });
    });

    // ปุ่มย้อนกลับ
    wizardNumberedBtnPrevList.forEach(btn =>
      btn.addEventListener('click', () => numberedStepper.previous())
    );

    // ปุ่มบันทึก (เหมือนของเดิม)
    if (wizardNumberedBtnSubmit) {
      wizardNumberedBtnSubmit.addEventListener('click', event => {
        event.preventDefault();
        let form = $('#expensefrm')[0];
        let formData = new FormData(form);
        const pageTech = $('#pageTech').val();

        wizardNumberedBtnSubmit.disabled = true;
        Swal.fire({
          title: 'กำลังบันทึกข้อมูล...',
          text: 'โปรดรอสักครู่',
          allowOutsideClick: false,
          showConfirmButton: false,
          showCancelButton: false,
          didOpen: () => Swal.showLoading()
        });

        $.ajax({
          url: '/Expense/Save',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          success: function (response) {
            Swal.close();
            if (response) {
              Swal.fire({
                title: response.message,
                icon: response.class,
                customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
                buttonsStyling: false,
              }).then(result => {
                if (result.isConfirmed) {
                  window.location.href = pageTech == 1 ? '/TechClaim' : '/Expense';
                }
              });
            }
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
            $(wizardNumberedBtnSubmit).prop("disabled", false);
          }
        });
      });
    }
  }
})();
