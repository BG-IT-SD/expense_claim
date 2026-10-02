$ (document).ready (function () {
  $ ('#saveButton').click (function () {
    $ ('#frmTypegroups').submit (); // ✅ สั่งให้ฟอร์มส่งข้อมูล
  });
  $ ('#Grouptypetable').DataTable ({
    processing: true,
    order: [[0, 'desc']],
    lengthMenu: [5, 10, 25, 50, 75, 100],
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
          window.location.href = '/Typegroup/create'; // เปลี่ยน URL ที่ต้องการไป
        },
      },
    ],
  });
  $ ('div.head-label').html (
    '<h5 class="card-title mb-0"><span class="mdi mdi-list-box"></span> รายการประเภทกลุ่ม</h5>'
  );

 // Delete
 $ (document).on ('click', '.deletetype', function () {
    var id = $ (this).data ('id');
    Swal.fire ({
      title: 'คุณต้องการลบข้อมูลหรือไม่?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
    }).then (result => {
      if (result.isConfirmed) {
        var url = TypegroupDelUrl.replace(':id', id);
        $.ajax ({
          url: url,
          type: 'POST',
          data: {
            id: id,
            _method: 'DELETE',
            _token:  $ ('meta[name="csrf-token"]').attr ('content'),
          },
          success: function (response) {
            if (response) {
              Swal.fire ({
                title: response.message,
                icon: response.class,
                customClass: {
                  confirmButton: 'btn btn-primary waves-effect waves-light',
                },
                buttonsStyling: false,
              }).then (result => {
                if (result.isConfirmed) {
                  window.location.href = '/Typegroup';
                }
              });
            }
          },
        });
      }
    });
  });
  // End Delete
});
