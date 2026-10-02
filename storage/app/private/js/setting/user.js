$ (document).ready (function () {
  // Datatable
  $ ('#datatables-users').DataTable ({
    processing: true,
    order: [[0, 'desc']],
    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 7,
    lengthMenu: [7, 10, 25, 50, 75, 100],
    buttons: [
      {
        extend: 'collection',
        className: 'btn btn-label-primary dropdown-toggle me-2 waves-effect waves-light',
        text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
        buttons: [
          {
            extend: 'print',
            text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
            className: 'dropdown-item',
            exportOptions: {
              columns: [0, 1, 2],
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML (inner);
                  var result = '';
                  $.each (el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains ('user-name')
                    ) {
                      result = result + item.lastChild.firstChild.textContent;
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
            customize: function (win) {
              //customize print view for dark
              $ (win.document.body)
                .css ('color', config.colors.headingColor)
                .css ('border-color', config.colors.borderColor)
                .css ('background-color', config.colors.bodyBg);
              $ (win.document.body)
                .find ('table')
                .addClass ('compact')
                .css ('color', 'inherit')
                .css ('border-color', 'inherit')
                .css ('background-color', 'inherit');
            },
          },
          {
            extend: 'csv',
            text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
            className: 'dropdown-item',
            exportOptions: {
              columns: [0, 1, 2, 3, 4],
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML (inner);
                  var result = '';
                  $.each (el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains ('user-name')
                    ) {
                      result = result + item.lastChild.firstChild.textContent;
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
          },
          {
            extend: 'excel',
            text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
            className: 'dropdown-item',
            exportOptions: {
              columns: [0, 1, 2, 3, 4],
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML (inner);
                  var result = '';
                  $.each (el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains ('user-name')
                    ) {
                      result = result + item.lastChild.firstChild.textContent;
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
          },
          {
            extend: 'pdf',
            text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
            className: 'dropdown-item',
            exportOptions: {
              columns: [0, 1, 2, 3, 4],
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML (inner);
                  var result = '';
                  $.each (el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains ('user-name')
                    ) {
                      result = result + item.lastChild.firstChild.textContent;
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
          },
          {
            extend: 'copy',
            text: '<i class="mdi mdi-content-copy me-1" ></i>Copy',
            className: 'dropdown-item',
            exportOptions: {
              columns: [0, 1, 2, 3, 4],
              // prevent avatar to be display
              format: {
                body: function (inner, coldex, rowdex) {
                  if (inner.length <= 0) return inner;
                  var el = $.parseHTML (inner);
                  var result = '';
                  $.each (el, function (index, item) {
                    if (
                      item.classList !== undefined &&
                      item.classList.contains ('user-name')
                    ) {
                      result = result + item.lastChild.firstChild.textContent;
                    } else if (item.innerText === undefined) {
                      result = result + item.textContent;
                    } else result = result + item.innerText;
                  });
                  return result;
                },
              },
            },
          },
        ],
      },
      {
        text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
        className: 'create-new btn btn-primary waves-effect waves-light',
        action: function (e, dt, node, config) {
          window.location.href = '/User/create'; // เปลี่ยน URL ที่ต้องการไป
        },
      },
    ],
    responsive: {
      details: {
        display: $.fn.dataTable.Responsive.display.modal ({
          header: function (row) {
            var data = row.data ();
            return 'Details of ' + data['full_name'];
          },
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          var data = $.map (columns, function (col, i) {
            return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
              ? '<tr data-dt-row="' +
                  col.rowIndex +
                  '" data-dt-column="' +
                  col.columnIndex +
                  '">' +
                  '<td>' +
                  col.title +
                  ':' +
                  '</td> ' +
                  '<td>' +
                  col.data +
                  '</td>' +
                  '</tr>'
              : '';
          }).join ('');

          return data
            ? $ ('<table class="table"/><tbody />').append (data)
            : false;
        },
      },
    },
  });
  $ ('div.head-label').html (
    '<h5 class="card-title mb-0"><span class="mdi mdi-list-box"></span> รายการผู้ใช้งาน</h5>'
  );

  $ ('#RoleTable').DataTable ({
    processing: true,
    order: [[0, 'desc']],
    lengthMenu: [5, 10, 25, 50, 75, 100],
    ajax: function(data, callback, settings) {
        var userid = $('#userid').val(); // Get userid from modal input field

        $.ajax({
            url: UserRoleListUrl, // Your API endpoint
            type: 'GET', // or 'POST' if needed
            data: {
                userid: userid, // Pass userid dynamically
            },
            success: function(response) {
                callback(response);
            }
        });
    },
    columns: [
      {
        data: 'id',
        name: 'id',
      },
      {
        data: 'module',
        name: 'module',
      },
      {
        data: 'role',
        name: 'role',
      },
      {
        data: 'status',
        name: 'status',
      },
      {
        data: 'action',
        name: 'action',
      },
    ],
  });
  $('#RolesModal').on('shown.bs.modal', function() {
    $('#RoleTable').DataTable().ajax.reload();
});
  // End Datatable

  // Role
$(document).on('click', '.rolemodal', function () {
    let Fullname = $ (this).data ('fullname');
    let id = $ (this).data ('id');
    let empid = $ (this).data ('empid');
    $ ('#RolesModal').modal ('show');
    $ ('#GroupsModalLabel').html ('Roles ' + empid + ' | ' + Fullname);
    $ ('#userid').val (id);
  });
  // End Role
  // Add Role
  $ ('#btnAddRole').click (function () {
    $ ('.text-danger').html ('');
    var userid = $ ('#userid').val ();
    var status = $ ('#status').val ();
    var roleid = $ ('#roleid').val ();
    var moduleid = $ ('#moduleid').val ();
    $.ajax ({
      url: RoleStoreUrl,
      type: 'POST',
      data: {
        _method: 'POST',
        _token: $ ('meta[name="csrf-token"]').attr ('content'),
        userid: userid,
        moduleid: moduleid,
        roleid: roleid,
        status: status,
      },
      success: function (response) {
        if (response) {
          $('#RoleTable').DataTable().ajax.reload();
          $ ('#moduleid').val ('');
          $ ('#status').val (1);
          $ ('#roleid').val ('');
          Swal.fire ({
            title: response.message,
            icon: response.class,
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
  // Add Role

// Edit Role
$(document).on('click', '.btnroleedit', function() {
var id = $(this).data('id');
$.ajax({
    url: "/UserRole/" + id + "/edit",
    type: "GET",
    success: function(response) {
        if (response) {

            $("#RolesModal").modal('show');
            $("#moduleid").val(response.data.moduleid);
            $("#status").val(response.data.status);
            $("#roleid").val(response.data.roleid);
            $("#userid").val(response.data.userid);
            $("#btnAddRole").addClass('hidden');
            $("#btnEditRole").removeClass('hidden');
            $("#btnEditRole").attr('data-id', response.data.id);
            $("#idedit").val(response.data.id);
        }
    }
});
});

$(document).on('click', '#btnEditRole', function() {
    $('.text-danger').html('');
    var moduleid = $("#moduleid").val();
    var status = $("#status").val();
    var roleid = $("#roleid").val();
    var userid = $("#userid").val();
    // var id = $(this).data('id');
    var id = $("#idedit").val();
    // console.log(id);
    $.ajax({
        url : RoleUpdateUrl.replace(':id', id),
        type: "POST",
        data: {
            _method: "PUT", // Laravel requires this for AJAX PUT requests
            _token: $ ('meta[name="csrf-token"]').attr ('content'),
            moduleid: moduleid,
            roleid: roleid,
            status: status,
            userid: userid,
        },
        success: function(response) {
            if (response) {
                $('#RoleTable').DataTable().ajax.reload();
                $("#moduleid").val('');
                $("#status").val(1);
                $("#roleid").val('');
                $("#btnAddRole").removeClass('hidden');
                $("#btnEditRole").addClass('hidden');
                Swal.fire({
                    title: response.message,
                    icon: response.class,
                    customClass: {
                        confirmButton: 'btn btn-primary waves-effect waves-light'
                    },
                    buttonsStyling: false
                })
            }
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;

            // Display validation errors
            if (errors) {
                $.each(errors, function(field, message) {
                    $('#' + field + '-error').html(message[0]);
                });
            }
        }
    });
});
// End Edit Role
// Reeet Role
 $("#btnResetRole").click(function() {
    $("#moduleid").val('');
    $("#status").val(1);
    $("#roleid").val('');
    $("#btnAddRole").removeClass('hidden');
    $("#btnEditRole").addClass('hidden');
});

// Delete Role
$(document).on('click', '.deleterole', function() {
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
            // var url = "/Groupprice/" + id;
            var url = RoleDelUrl.replace(':id', id);
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
                        $('#RoleTable').DataTable().ajax.reload();
                        Swal.fire({
                            title: response.message,
                            icon: response.class,
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        })
                    }
                }
            });
        }
    })
});

// Delete User
$(document).on('click', '.deleteuser', function() {
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
            var url = UserDelUrl.replace(':id', id);
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


