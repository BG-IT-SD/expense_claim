$ (document).ready (function () {
  $ ('#content-register').hide ();
  $ ('#saveButton').hide ();
  $ ('#checkempid').click (function () {
    let empid = $ ('#empid').val ();
    let idcard = $ ('#idcard').val ();
    $.ajax ({
      url: CheckEmpURL,
      type: 'POST',
      data: {
        empid: empid,
        idcard: idcard,
        _token: $ ('meta[name="csrf-token"]').attr ('content'),
      },
      success: function (response) {
        if (response.status == 200) {
          // console.log(response.employees.EMAIL);
          $ ('#content-check').hide ();
          $ ('#content-register').show ();
          $ ('#text-email').text (response.employees.EMAIL);
          $ ('#text-empid').text (response.employees.CODEMPID);
          $ ('#checkEmpid').val (response.employees.CODEMPID);
          $ ('#text-fullname').text (
            response.employees.NAMFIRSTT + ' ' + response.employees.NAMLASTT
          );
          $ ('#text-dept').text (response.employees.DEPT);
          $ ('#text-bu').text (response.employees.alias_name);
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
        var errors = xhr.responseJSON.errors;

        // Display validation errors
        if (errors) {
          $.each (errors, function (field, message) {
            $ ('#' + field + '-error').html (message[0]);
          });
        }
      },
    });
  });

  $ ('#saveButton').click (function () {
    let empiddata = $ ('#checkEmpid').val ();
    let password = $ ('#password').val ();
    let repassword = $("#password_confirmation").val();
    $.ajax ({
      url: RegisterURL,
      type: 'POST',
      data: {
        empid: empiddata,
        password: password,
        repassword: repassword,
        _token: $ ('meta[name="csrf-token"]').attr ('content'),
      },
      success: function (response) {
        if (response.status == 200) {
          // console.log(response);
          Swal.fire ({
            title: response.message,
            icon: 'success',
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light',
            },
            buttonsStyling: false,
          }).then (result => {
            if (result.isConfirmed) {
              window.location.href = '/User';
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
      error: function (xhr) {
        var errors = xhr.responseJSON.errors;

        // Display validation errors
        if (errors) {
          $.each (errors, function (field, message) {
            $ ('#' + field + '-error').html (message[0]);
          });
        }
      },
    });
  });

  $('#resetpassword').click(function(){
    Swal.fire({
        title: 'คุณต้อง reset password หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed){
            let id = $("#userid").val();
            let empreset = $("#empid").val();
            var url = UserResetUrl.replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _method: "PUT", // Laravel requires this for AJAX PUT requests
                    _token: $ ('meta[name="csrf-token"]').attr ('content'),
                    id: id,
                    empid : empreset,
                },
                success: function(response) {
                    if (response) {
                        Swal.fire({
                            title: response.message,
                            icon: response.class,
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        }).then (result => {
                            if (result.isConfirmed) {
                                location.reload ();
                            }
                          });
                    }
                }
            });
        }
    });
  });


  $('#signatureForm').on('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    $.ajax({
        url: UserSigUrl,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: res.message,
                confirmButtonText: 'ตกลง',
            }).then(() => {
                location.reload();
            });

            $('#signatureModal').modal('hide');
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดในการอัปโหลด',
                confirmButtonText: 'ปิด'
            });
        }
    });
});


});
