$(document).ready(function() {
    // DataTable
    $('#GroupsTable').DataTable({
        processing: true,
        ajax: {
            url: GroupListUrl, // Replace with your route
            type: "GET" // or "POST" if required
        },
        order: [
            [0, 'desc']
        ],
        displayLength: 5,
        lengthMenu: [5, 10, 25, 50, 75, 100],
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'groupname',
                name: 'groupname'
            },
            {
                data: 'levelname',
                name: 'levelname'
            },
            {
                data: 'status',
                name: 'status'

            }, {
                data: 'action',
                name: 'action'
            }
        ],
        // dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        // buttons: [
        //     {
        //         text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
        //         className: 'create-new btn btn-primary waves-effect waves-light',
        //         attr: {
        //             'data-bs-toggle': 'modal',
        //             'data-bs-target': '#GroupsAddModal'
        //         }
        //     }
        // ],
    });

    // Main
    $('#PricePerMealTable').DataTable({
        processing: true,
        ajax: {
            url: ListUrl, // Replace with your route
            type: "GET" // or "POST" if required
        },
        order: [
            [0, 'desc']
        ],
        displayLength: 5,
        lengthMenu: [5, 10, 25, 50, 75, 100],
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'groupname',
                name: 'groupname'
            },
            {
                data: 'status',
                name: 'status'

            }, {
                data: 'action',
                name: 'action'
            }
        ],
        dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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
                },
                {
                  extend: 'excel',
                  text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
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
                },
                {
                  extend: 'pdf',
                  text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
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
                },
                {
                  extend: 'copy',
                  text: '<i class="mdi mdi-content-copy me-1" ></i>Copy',
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
                },
              ],
            },
            {
              text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
              className: 'create-new btn btn-primary waves-effect waves-light',
              action: function (e, dt, node, config) {
                window.location.href = '/Pricepermeal/create'; // เปลี่ยน URL ที่ต้องการไป
            }
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
    $('div.head-label').html('<h5 class="card-title mb-0"><span class="mdi mdi-list-box"></span> รายการราคาต่อมื้อ</h5>');
    // End DataTable

    // Manage Group
    $("#btnAddGroups").click(function() {
        $('.text-danger').html('');
        var groups = $("#groups").val();
        var status = $("#status").val();
        var level = $("#level").val();
        $.ajax({
            url: GroupStoreUrl,
            type: "POST",
            data: {
                groups: groups,
                status: status,
                level: level
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response) {

                    $('#GroupsTable').DataTable().ajax.reload();
                    $("#groups").val('');
                    $("#status").val(1);
                    $("#level").val('');
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
    // Delete Group
    $(document).on('click', '.deletegroup', function() {
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
                var url = GroupDelUrl.replace(':id', id);
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "DELETE", // Laravel requires this for AJAX PUT requests
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response) {
                            $('#GroupsTable').DataTable().ajax.reload();
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
    // End Delete Group
    // Edit Group
    $(document).on('click', '.btngroupedit', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "/Groupprice/" + id + "/edit",
            type: "GET",
            success: function(response) {
                if (response) {
                    console.log($("#btnAddGroups").length);
                    $("#GroupsModal").modal('show');
                    $("#groups").val(response.data.groupname);
                    $("#status").val(response.data.status);
                    $("#level").val(response.data.levelid);
                    $("#btnAddGroups").addClass('hidden');
                    $("#btnEditGroups").removeClass('hidden');
                    $("#btnEditGroups").attr('data-id', response.data.id);
                    $("#idedit").val(response.data.id);
                }
            }
        });
    });

    $(document).on('click', '#btnEditGroups', function() {
        $('.text-danger').html('');
        var groups = $("#groups").val();
        var status = $("#status").val();
        var level = $("#level").val();
        // var id = $(this).data('id');
        var id = $("#idedit").val();
        // console.log(id);
        $.ajax({
            // url: "/Groupprice/" + id,
            url : GroupUpdateUrl.replace(':id', id),
            type: "POST",
            data: {
                _method: "PUT", // Laravel requires this for AJAX PUT requests
                groups: groups,
                status: status,
                level: level
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response) {
                    $('#GroupsTable').DataTable().ajax.reload();
                    $("#groups").val('');
                    $("#status").val(1);
                    $("#level").val('');
                    $("#btnAddGroups").removeClass('hidden');
                    $("#btnEditGroups").addClass('hidden');
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
    // End Edit Group
    // Reeet Group
    $("#btnResetGroups").click(function() {
        $("#groups").val('');
        $("#status").val(1);
        $("#level").val('');
        $("#btnAddGroups").removeClass('hidden');
        $("#btnEditGroups").addClass('hidden');
    });
    // End Manage Group

    // Delete
  $(document).on('click', '.deletemeal', function() {
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
            // var url = "/Pricepermeal/" + id;
            var url = DelMealUrl.replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: id,
                    _method: "DELETE", // Laravel requires this for AJAX PUT requests
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response) {
                        $('#PricePerMealTable').DataTable().ajax.reload();
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
// End Delete

});
