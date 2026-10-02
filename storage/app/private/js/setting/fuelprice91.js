$ (document).ready (function () {
  $ ('#StartDate').flatpickr ({
    monthSelectorType: 'static',
  });
  $ ('#EndDate').flatpickr ({
    monthSelectorType: 'static',
  });
  $ ('#dateprice').flatpickr ({
    monthSelectorType: 'static',
  });

  //   DataTable
  $ ('#FuelPrice91Table').DataTable ({
    processing: true,
    order: [[2, 'desc']],
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
        attr: {
          'data-bs-toggle': 'modal',
          'data-bs-target': '#AddPriceModal',
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
    '<h5 class="card-title mb-0"><span class="mdi mdi-list-box"></span> รายการราคาน้ำมัน Gasohol 91</h5>'
  );

  // End DataTable

  // Add Price
  $ ('.addprice').click (function () {
    $ ('#AddPriceModalLabel').text ('Add');
    $ ('#dateprice').val ('');
    $ ('#price').val ('');
    $ ('#id').val ('');
  });
  $ ('#submitBtn').click (function () {
    var form = $ ('#fuelPriceForm');

    var fuelPriceIdEdit = $ ('#id').val ();
    $ ('.text-danger').html ('');
    if (fuelPriceIdEdit) {
      var url = FuelpriceUpdateUrl.replace (':id', fuelPriceIdEdit); // Replace ID dynamically
      var method = 'PUT'; // Use PUT for updating
    } else {
      var url = FuelpriceStoreUrl;
      var method = 'POST'; // Use POST for new entries
    }

    var formData = form.serializeArray (); // Serialize form data as an array

    // Add Laravel _method and CSRF token manually
    formData.push ({name: '_method', value: method});
    formData.push ({
      name: '_token',
      value: $ ('meta[name="csrf-token"]').attr ('content'),
    });
    $.ajax ({
      url: url,
      type: 'POST',
      data: formData,
      success: function (response) {
        $ ('#AddPriceModal').modal ('hide');
        // console.log(response);
        if (response) {
          Swal.fire ({
            title: response.message,
            // text: response.message,
            icon: response.class,
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light',
            },
            buttonsStyling: false,
          }).then (result => {
            if (result.isConfirmed) {
              location.reload ();
            }
          });
        }
      },
      error: function (xhr) {
        var errors = xhr.responseJSON.errors;

        // Display validation errors
        if (errors) {
          $.each (errors, function (field, message) {
            $ ('#' + field + '-error').html (message[0]); // Show error message below the field
          });
        }
      },
    });
  });
  // End Add Price
  // Del Price
  $ ('.delprice').click (function () {
    var id = $ (this).data ('bs-id');
    $ ('#idprice').val (id);
  });

  $ ('#ConfirmBtn').click (function () {
    var formDel = $ ('#fuelDelPriceForm');
    var formDelData = {
      _method: 'DELETE', // Laravel method spoofing
      _token: $ ('meta[name="csrf-token"]').attr ('content'), // CSRF token for security
    };

    var fuelPriceId = formDel.find ('input[name="idprice"]').val ();

    // var url = '/FuelPrice91/' + fuelPriceId;
    var url = FuelpriceDelUrl.replace (':id', fuelPriceId);
    $.ajax ({
      url: url,
      type: 'POST',
      data: formDelData,
      success: function (response) {
        $ ('#DelPriceModal').modal ('hide');

        // console.log(response);
        if (response) {
          Swal.fire ({
            title: response.message,
            // text: response.message,
            icon: response.class,
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light',
            },
            buttonsStyling: false,
          }).then (result => {
            if (result.isConfirmed) {
              location.reload ();
            }
          });
        }
      },
      error: function (xhr) {
        var errors = xhr.responseJSON.errors;
      },
    });
  });
  // End Del Price
  // Edit Price
  $ ('.editprice').click (function () {
    $ ('#AddPriceModalLabel').text ('Edit');
    var id = $ (this).data ('bs-id');
    var url = '/FuelPrice91/' + id + '/edit';
    $.ajax ({
      url: url,
      type: 'GET',
      success: function (response) {
        console.log (response);
        // convert date
        var datetime = response.dateprice;
        var dateOnly = datetime.replace (/ .*/, ''); // Removes everything after the space
        // end convert date
        // Update Flatpickr
        var flatpickrInstance = $ ('#dateprice').flatpickr ();
        flatpickrInstance.setDate (dateOnly, true); // true triggers change event
        $ ('#id').val (response.id);
        $ ('#price').val (response.price);
      },
      error: function (xhr) {
        var errors = xhr.responseJSON.errors;
      },
    });
  });
  // End Edit Price
});
