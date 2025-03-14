/**
 *  Page auth register multi-steps
 */

'use strict';

// Multi Steps Validation
// --------------------------------------------------------------------
document.addEventListener ('DOMContentLoaded', function (e) {
  (function () {
    const stepsValidation = document.querySelector ('#multiStepsValidation');
    if (typeof stepsValidation !== undefined && stepsValidation !== null) {
      // Multi Steps form
      const stepsValidationForm = stepsValidation.querySelector (
        '#multiStepsForm'
      );
      // Form steps
      const stepsValidationFormStep1 = stepsValidationForm.querySelector (
        '#accountDetailsValidation'
      );
      const stepsValidationFormStep2 = stepsValidationForm.querySelector (
        '#personalInfoValidation'
      );
      const stepsValidationNext = [].slice.call (
        stepsValidationForm.querySelectorAll ('.btn-next')
      );
      const stepsValidationPrev = [].slice.call (
        stepsValidationForm.querySelectorAll ('.btn-prev')
      );

      let validationStepper = new Stepper (stepsValidation, {
        linear: true,
      });

      // Account details
      const multiSteps1 = FormValidation.formValidation (
        stepsValidationFormStep1,
        {
          fields: {
            multiStepsEmpid: {
              validators: {
                notEmpty: {
                  message: 'Please enter Employee ID',
                },
                stringLength: {
                  min: 6,
                  max: 20,
                  message: 'The id must be more than 6 and less than 20 characters long',
                },
                regexp: {
                  regexp: /^[a-zA-Z0-9 ]+$/,
                  message: 'The name can only consist of alphabetical, number and space',
                },
              },
            },
            multiStepsIDCard: {
              validators: {
                notEmpty: {
                  message: 'Please enter ID Card',
                },
              },
            },
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger (),
            bootstrap5: new FormValidation.plugins.Bootstrap5 ({
              // Use this for enabling/changing valid/invalid class
              // eleInvalidClass: '',
              eleValidClass: '',
              rowSelector: '.form-floating',
            }),
            autoFocus: new FormValidation.plugins.AutoFocus (),
            submitButton: new FormValidation.plugins.SubmitButton (),
          },
          init: instance => {
            instance.on ('plugins.message.placed', function (e) {
              if (e.element.parentElement.classList.contains ('input-group')) {
                e.element.parentElement.insertAdjacentElement (
                  'afterend',
                  e.messageElement
                );
              }
            });
          },
        }
      ).on ('core.form.valid', function () {
        // Check ajax
        $.ajax ({
          url: '/CheckEmpID',
          type: 'POST',
          data: {
            empid: stepsValidationFormStep1.querySelector (
              '[name="multiStepsEmpid"]'
            ).value,
            idcard: stepsValidationFormStep1.querySelector (
              '[name="multiStepsIDCard"]'
            ).value,
          },
          headers: {
            'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content'),
            "Accept": "application/json",
          },
          success: function (response) {
            console.log (response);
            if (response.status === 200) {
              stepsValidationFormStep2.querySelector (
                '[name="checkEmpid"]'
              ).value =
                response.employees.CODEMPID;
              //   console.log(response.employees[0].CODEMPID);
              validationStepper.next ();
            } else {
              Swal.fire ({
                title: response.message,
                icon: 'error',
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light',
                },
                buttonsStyling: false,
              });
            }
          },
          error: function (xhr) {
            console.log("XHR Error Response:", xhr); // Debugging

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                let errorMessages = "";

                $.each(errors, function(field, messages) {
                    errorMessages += messages[0] + "\n"; // Show first message for each field
                });

                Swal.fire({
                    title: "Validation Error",
                    text: errorMessages,
                    icon: "error",
                    customClass: {
                        confirmButton: "btn btn-primary waves-effect waves-light"
                    },
                    buttonsStyling: false
                }).then (result => {
                    if (result.isConfirmed) {
                      location.href = '/login';
                    }
                  });
            } else {
                Swal.fire({
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    icon: "error",
                    customClass: {
                        confirmButton: "btn btn-primary waves-effect waves-light"
                    },
                    buttonsStyling: false
                });
            }
          },
        });
      });

      // Personal info
      const multiSteps2 = FormValidation.formValidation (
        stepsValidationFormStep2,
        {
          fields: {
            multiStepsFirstName: {
              validators: {
                notEmpty: {
                  message: 'Please enter first name',
                },
              },
            },
            multiStepsAddress: {
              validators: {
                notEmpty: {
                  message: 'Please enter your address',
                },
              },
            },
            multiStepsPass: {
              validators: {
                notEmpty: {
                  message: 'Please enter password',
                },
              },
            },
            multiStepsConfirmPass: {
              validators: {
                notEmpty: {
                  message: 'Confirm Password is required',
                },
                identical: {
                  compare: function () {
                    return stepsValidationFormStep2.querySelector (
                      '[name="multiStepsPass"]'
                    ).value;
                  },
                  message: 'The password and its confirm are not the same',
                },
              },
            },
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger (),
            bootstrap5: new FormValidation.plugins.Bootstrap5 ({
              // Use this for enabling/changing valid/invalid class
              // eleInvalidClass: '',
              eleValidClass: '',
              rowSelector: function (field, ele) {
                // field is the field name
                // ele is the field element
                switch (field) {
                  case 'multiStepsFirstName':
                    return '.col-sm-6';
                  case 'multiStepsAddress':
                    return '.col-md-12';
                  default:
                    return '.row';
                }
              },
            }),
            autoFocus: new FormValidation.plugins.AutoFocus (),
            submitButton: new FormValidation.plugins.SubmitButton (),
          },
        }
      ).on ('core.form.valid', function () {
        // Submit Password
        $.ajax ({
          url: '/register',
          type: 'POST',
          data: {
            empid: stepsValidationFormStep2.querySelector (
              '[name="checkEmpid"]'
            ).value,
            password: stepsValidationFormStep2.querySelector (
              '[name="multiStepsPass"]'
            ).value,
          },
          headers: {
            'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content'),
             "Accept": "application/json",
          },
          success: function (response) {
            // console.log (response);
            if (response.status === 200) {
              Swal.fire ({
                title: response.message,
                icon: 'success',
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light',
                },
                buttonsStyling: false,
              }).then (result => {
                if (result.isConfirmed) {
                  location.href = '/login';
                }
              });
            } else {
              Swal.fire ({
                title: response.message,
                icon: 'error',
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light',
                },
                buttonsStyling: false,
              });
            }
          },
          error: function(error) {

        }
        ,
        });
      });

      stepsValidationNext.forEach (item => {
        item.addEventListener ('click', event => {
          // When click the Next button, we will validate the current step
          switch (validationStepper._currentIndex) {
            case 0:
              multiSteps1.validate ();
              break;

            case 1:
              multiSteps2.validate ();
              break;

            default:
              break;
          }
        });
      });

      stepsValidationPrev.forEach (item => {
        item.addEventListener ('click', event => {
          switch (validationStepper._currentIndex) {
            case 2:
              validationStepper.previous ();
              break;

            case 1:
              validationStepper.previous ();
              break;

            case 0:

            default:
              break;
          }
        });
      });
    }
  }) ();
});
