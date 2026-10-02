$ (document).ready (function () {
  $ ('#hrgroup').DataTable ({
    processing: true,
    order: [[0, 'asc']],
    // lengthMenu: [5, 10, 25, 50, 75, 100],
  });

  $ ('#hrgrouplist').DataTable ({
    processing: true,
    order: [[0, 'desc']],
    // lengthMenu: [5, 10, 25, 50, 75, 100],
  });

  $ ('#content-register').hide ();

  $ ('#checkempid').click (function () {
    let empid = $ ('#empid').val ();
    // let idcard = $ ('#idcard').val ();
    $.ajax ({
      url: CheckEmpURL,
      type: 'POST',
      data: {
        empid: empid,
        // idcard: idcard,
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

          //value to submit
          $ ('#emp_data').val (response.employees.CODEMPID);
          $ ('#email_data').val (response.employees.EMAIL);
          $ ('#name_data').val (
            response.employees.NAMFIRSTT + ' ' + response.employees.NAMLASTT
          );
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

  if ($ ('#listhr').length) {
    //List From HRMS
    $ ('#listhr').select2 ({
      ajax: {
        url: '/HRgroup/listemphrms',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            sKeyword: params.term,
            page: params.page || 1,
            group: $('#groupid').val(),
            step: $('#step').val()
          };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.data,
            pagination: {
              more: params.page * 5 < data.total_count,
            },
          };
        },
        cache: true,
      },
      minimumInputLength: 1,

      //เพิ่มตรงนี้เพื่อให้ตอนเลือกแล้ว แสดง email | ชื่อ
      templateSelection: function (data) {
        if (data.text) {
          return data.text;
        }
        return data.email + ' | ' + data.name; // fallback ถ้าจัดแยกไว้
      },
    });

    $ ('#listhr').change (function () {
      var id = $ (this).val ();
      $.ajax ({
        url: '/HRgroup/emphrmsdata',
        type: 'get',
        data: 'emid=' + id,
        dataType: 'json',
        success: function (data) {
          // console.log (data);
          $ ('#head_email').val (data.Emailemp);
          $ ('#head_name').val (data.Nameemp);
          $ ('#head_id').val (data.Idemp);
        },
        error: function (data) {},
      });
    });
  }

//   Delete User
$(document).on('click', '.deleteplant', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: 'คุณต้องการลบข้อมูลหรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = PlantDelUrl.replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _method: "DELETE", // Laravel requires this for AJAX PUT requests
                    _token: $ ('meta[name="csrf-token"]').attr ('content'),
                    id: id
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
    })
});
});
