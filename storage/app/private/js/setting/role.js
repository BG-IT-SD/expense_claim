$ (document).ready (function () {
  $ ('#saveButton').click (function () {
    $ ('#frmPermission').submit (); // ✅ สั่งให้ฟอร์มส่งข้อมูล
  });
  $ ('#datatables-module').DataTable ({
    processing: true,
    order: [[0, 'desc']],
    dom: '<"card-header flex-column flex-md-row"<"head-label_module text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 5,
    lengthMenu: [5, 10, 25, 50, 75, 100],
    buttons: [
      {
        text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
        className: 'create-new btn btn-primary waves-effect waves-light',
        action: function (e, dt, node, config) {
          window.location.href = '/Role/create/1'; // เปลี่ยน URL ที่ต้องการไป
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
  $ ('div.head-label_module').html (
    '<h5 class="card-title mb-0"><span class="mdi mdi-list-box"></span> รายการหน้าจอการใช้งาน</h5>'
  );

  //   Role

  $ ('#datatables-role').DataTable ({
    processing: true,
    order: [[0, 'desc']],
    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 5,
    lengthMenu: [5, 10, 25, 50, 75, 100],
    buttons: [
      {
        text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
        className: 'create-new btn btn-primary waves-effect waves-light',
        action: function (e, dt, node, config) {
          window.location.href = '/Role/create/2'; // เปลี่ยน URL ที่ต้องการไป
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
    '<h5 class="card-title mb-0"><span class="mdi mdi-list-box"></span> รายการสิทธิการใช้งาน</h5>'
  );
});

function deleteRole(roleId, roleType) {
    Swal.fire({
        title: 'คุณต้องการลบข้อมูลหรือไม่?', // "Do you want to delete this record?"
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
    }).then((result) => {
        if (result.isConfirmed) {
            // let deleteUrl = ModuleDelUrl.replace(':id', roleId).replace(':type', roleType);
            let deleteUrl = "/Role/"+roleId+"/"+roleType;

            $.ajax({
                url: deleteUrl,
                type: 'POST', // Always POST, but `_method: DELETE` is included
                data: {
                    _method: 'DELETE', // Laravel recognizes this as DELETE
                    _token: $('meta[name="csrf-token"]').attr('content'), // ✅ FIXED SPACING
                },
                success: function(response) {
                    if (response) {
                        Swal.fire({
                            title: response.message || 'Deleted Successfully!',
                            icon: response.class || 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light',
                            },
                            buttonsStyling: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // Refresh the page after confirmation
                            }
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด!', // "Error!"
                        text: 'ไม่สามารถลบข้อมูลได้.', // "Could not delete the record."
                        icon: 'error',
                    });
                },
            });
        }
    });
}
