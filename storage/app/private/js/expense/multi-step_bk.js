(function () {
  // Numbered Wizard
  // --------------------------------------------------------------------
  const wizardNumbered = document.querySelector ('.wizard-numbered'),
    wizardNumberedBtnNextList = [].slice.call (
      wizardNumbered.querySelectorAll ('.btn-next')
    ),
    wizardNumberedBtnPrevList = [].slice.call (
      wizardNumbered.querySelectorAll ('.btn-prev')
    ),
    wizardNumberedBtnSubmit = wizardNumbered.querySelector ('.btn-submit');

  if (typeof wizardNumbered !== undefined && wizardNumbered !== null) {
    const numberedStepper = new Stepper (wizardNumbered, {
      linear: false,
    });

    // สร้าง FormValidation ให้กับ form หลัก
    const fv = FormValidation.formValidation (
      document.getElementById ('expensefrm'),
      {
        fields: {
          // Step 1
          returntime: {
            validators: {
              notEmpty: {
                message: 'กรุณากรอกเวลา',
              },
              regexp: {
                regexp: /^([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
                message: 'รูปแบบเวลาต้องเป็น HH:MM:SS เช่น 20:00:00',
              },
            },
          },
          // Step 2
          // amount: {
          //   validators: {
          //     notEmpty: {
          //       message: 'กรุณากรอกจำนวนเงิน',
          //     },
          //     numeric: {
          //       message: 'ต้องเป็นตัวเลขเท่านั้น',
          //     },
          //   },
          // },
          // Step 3
          headapprove: {
            validators: {
              notEmpty: {
                message: 'กรุณาเลือกผู้อนุมัติ',
              },
            },
          },
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger (),
          bootstrap5: new FormValidation.plugins.Bootstrap5 (),
          submitButton: new FormValidation.plugins.SubmitButton (),
        },
      }
    );


    if (wizardNumberedBtnNextList) {
      wizardNumberedBtnNextList.forEach (wizardNumberedBtnNext => {
        wizardNumberedBtnNext.addEventListener ('click', event => {
          event.preventDefault ();

          // หาว่าอยู่ใน step ปัจจุบัน
          const currentStep = wizardNumberedBtnNext.closest ('.content[step]');
          if (!currentStep) {
            console.warn ('⛔ ไม่พบ DOM .content[step] ที่ใกล้กับปุ่มนี้');
            return;
          }

          // ✅ รัน validate ทั้งฟอร์ม
          fv.validate ().then (function (status) {
            if (status === 'Valid') {
              //เช็กว่าเฉพาะ field ใน step ปัจจุบัน ไม่มี is-invalid
              const invalidFields = currentStep.querySelectorAll (
                '.is-invalid'
              );
              if (invalidFields.length === 0) {
                numberedStepper.next (); // ไปหน้าถัดไป
              } else {
                console.warn (
                  '⚠️ มี field ที่ยังไม่ผ่าน validation ใน step นี้'
                );
              }
            } else {
              console.warn ('⛔ ฟอร์มยังไม่ valid ทั้งชุด');
            }
          });
        });
      });
    }
    if (wizardNumberedBtnPrevList) {
      wizardNumberedBtnPrevList.forEach (wizardNumberedBtnPrev => {
        wizardNumberedBtnPrev.addEventListener ('click', event => {
          numberedStepper.previous ();
        });
      });
    }
    if (wizardNumberedBtnSubmit) {
        wizardNumberedBtnSubmit.addEventListener('click', event => {
          event.preventDefault();

          let form = $('#expensefrm')[0];
          let formData = new FormData(form);
          const pageTech = $('#pageTech').val();

          // ปิดปุ่มและแสดงโหลด
          wizardNumberedBtnSubmit.disabled = true;

          Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            text: 'โปรดรอสักครู่',
            allowOutsideClick: false,
            showConfirmButton: false,   // ไม่แสดงปุ่ม OK
            showCancelButton: false,    //ไม่แสดงปุ่ม Cancel
            didOpen: () => {
              Swal.showLoading();
            }
          });

        //   $.ajax({
        //     url: '/Expense/Save',
        //     type: 'POST',
        //     data: formData,
        //     processData: false,
        //     contentType: false,
        //     headers: {
        //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        //     },
        //     success: function (response) {
        //       Swal.close(); // ปิด loading

        //       if (response) {
        //         Swal.fire({
        //           title: response.message,
        //           icon: response.class,
        //           customClass: {
        //             confirmButton: 'btn btn-primary waves-effect waves-light',
        //           },
        //           buttonsStyling: false,
        //         }).then(result => {
        //           if (result.isConfirmed) {
        //             // window.location.href = '/Expense';
        //             if (pageTech == 1) {
        //                 window.location.href = '/TechClaim';
        //             } else {
        //                 window.location.href = '/Expense';
        //             }
        //           }
        //         });
        //       }
        //     },
        //     error: function (xhr) {
        //       Swal.close(); // ปิด loading

        //       Swal.fire({
        //         icon: 'error',
        //         title: 'เกิดข้อผิดพลาด',
        //         text: xhr.responseJSON?.message || 'ไม่สามารถบันทึกข้อมูลได้',
        //       });
        //       wizardNumberedBtnSubmit.disabled = false; // เปิดปุ่มใหม่
        //     },
        //   });
        $.ajax({
            url: '/Expense/Save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                Swal.close();

                if (response) {
                Swal.fire({
                    title: response.message,
                    icon: response.class,
                    customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light',
                    },
                    buttonsStyling: false,
                }).then(result => {
                    if (result.isConfirmed) {
                    if (pageTech == 1) {
                        window.location.href = '/TechClaim';
                    } else {
                        window.location.href = '/Expense';
                    }
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
                // เปิดปุ่มกลับ ถ้าไม่ได้ redirect
                $(wizardNumberedBtnSubmit).prop("disabled", false);
            }
            });
        });
      }

  }
}) ();
