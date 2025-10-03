/**
 *  Page auth register multi-steps
 */

'use strict';

// Multi Steps Validation
// --------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const stepsValidation = document.querySelector('#multiStepsValidation');
    if (!stepsValidation) return;

    // ----- form & panes -----
    const stepsValidationForm = stepsValidation.querySelector('#multiStepsForm');
    const stepsValidationFormStep1 = stepsValidationForm.querySelector('#accountDetailsValidation');
    const stepsValidationFormStep2 = stepsValidationForm.querySelector('#personalInfoValidation');
    const stepsValidationNext = [].slice.call(stepsValidationForm.querySelectorAll('.btn-next'));
    const stepsValidationPrev = [].slice.call(stepsValidationForm.querySelectorAll('.btn-prev'));

    // 1) ปิด native validation + บังคับปุ่มเป็น type=button
    stepsValidationForm.setAttribute('novalidate', 'novalidate');
    [...stepsValidationNext, ...stepsValidationPrev].forEach(btn => btn.setAttribute('type', 'button'));

    let validationStepper = new Stepper(stepsValidation, { linear: true });

    // 2) disable inputs ใน step ที่ไม่ active (กัน browser focus/validate ช่องที่ซ่อน)
    const syncDisabledInputs = () => {
      stepsValidationForm.querySelectorAll('.content').forEach(pane => {
        const active = pane.classList.contains('active');
        pane.querySelectorAll('input, select, textarea').forEach(el => { el.disabled = !active; });
      });
    };
    syncDisabledInputs();
    stepsValidation.addEventListener('shown.bs-stepper', syncDisabledInputs);

    // ---------- Step 1: Check Emp ----------
    const multiSteps1 = FormValidation.formValidation(stepsValidationFormStep1, {
      fields: {
        multiStepsEmpid: {
          validators: {
            notEmpty: { message: 'Please enter Employee ID' },
            stringLength: {
              min: 6, max: 20,
              message: 'The id must be more than 6 and less than 20 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9 ]+$/,
              message: 'The id can only consist of letters, numbers and space'
            }
          }
        },
        multiStepsIDCard: {
          validators: {
            notEmpty: { message: 'Please enter ID Card' },
            regexp: {
              regexp: /^\d{13}$/,
              message: 'ID Card must be 13 digits'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.form-floating'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    }).on('core.form.valid', function () {
      $.ajax({
        url: '/CheckEmpID',
        type: 'POST',
        data: {
          empid: stepsValidationFormStep1.querySelector('[name="multiStepsEmpid"]').value,
          idcard: stepsValidationFormStep1.querySelector('[name="multiStepsIDCard"]').value
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Accept': 'application/json'
        },
        success: function (response) {
          if (response.status === 200) {
            stepsValidationFormStep2.querySelector('[name="checkEmpid"]').value = response.employees.CODEMPID;
            validationStepper.next();
          } else {
            Swal.fire({
              title: response.message,
              icon: 'error',
              customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
              buttonsStyling: false
            });
          }
        },
        error: function (xhr) {
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            let msg = Object.values(xhr.responseJSON.errors).map(arr => arr[0]).join('\n');
            Swal.fire({
              title: 'Validation Error',
              text: msg,
              icon: 'error',
              customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
              buttonsStyling: false
            }).then(r => { if (r.isConfirmed) location.href = '/login'; });
          } else {
            Swal.fire({
              title: 'Error',
              text: 'An unexpected error occurred. Please try again.',
              icon: 'error',
              customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
              buttonsStyling: false
            });
          }
        }
      });
    });

    // ---------- Step 2: Create Password ----------
    const multiSteps2 = FormValidation.formValidation(stepsValidationFormStep2, {
      fields: {
        multiStepsPass: {
          validators: {
            notEmpty: { message: 'Please enter password' },
            regexp: {
              // ต้องมีตัวใหญ่ + ตัวเลข + ยาว ≥ 8
              regexp: /^(?=.*[A-Z])(?=.*\d).{8,}$/,
              message: 'Password must be at least 8 characters and include an uppercase letter and a number'
            }
          }
        },
        multiStepsConfirmPass: {
          validators: {
            notEmpty: { message: 'Confirm Password is required' },
            identical: {
              compare: function () {
                return stepsValidationFormStep2.querySelector('[name="multiStepsPass"]').value;
              },
              message: 'The password and its confirm are not the same'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.row'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus(),
        submitButton: new FormValidation.plugins.SubmitButton()
      }
    }).on('core.form.valid', function () {
      $.ajax({
        url: '/register',
        type: 'POST',
        data: {
          empid: stepsValidationFormStep2.querySelector('[name="checkEmpid"]').value,
          password: stepsValidationFormStep2.querySelector('[name="multiStepsPass"]').value,
          repassword: stepsValidationFormStep2.querySelector('[name="multiStepsConfirmPass"]').value
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Accept': 'application/json'
        },
        success: function (response) {
          if (response.status === 200) {
            Swal.fire({
              title: response.message,
              icon: 'success',
              customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
              buttonsStyling: false
            }).then(r => { if (r.isConfirmed) location.href = '/login'; });
          } else {
            Swal.fire({
              title: response.message,
              icon: 'error',
              customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
              buttonsStyling: false
            });
          }
        },
        error: function () {
          Swal.fire({
            title: 'Error',
            text: 'An unexpected error occurred. Please try again.',
            icon: 'error',
            customClass: { confirmButton: 'btn btn-primary waves-effect waves-light' },
            buttonsStyling: false
          });
        }
      });
    });

    // ----- control next/prev -----
    stepsValidationNext.forEach(btn => {
      btn.addEventListener('click', () => {
        switch (validationStepper._currentIndex) {
          case 0: multiSteps1.validate(); break;
          case 1: multiSteps2.validate(); break;
          default: break;
        }
      });
    });
    stepsValidationPrev.forEach(btn => {
      btn.addEventListener('click', () => {
        if (validationStepper._currentIndex > 0) validationStepper.previous();
      });
    });
  })();
});

