$ (function () {
  // flatpickr
  $ ('#exdate').flatpickr ({monthSelectorType: 'static'});
  $ ('#end_exdate').flatpickr ({monthSelectorType: 'static'});

  // กันกรณีลืมประกาศ URL
  if (typeof HR_REPORT_DATA_URL === 'undefined') {
    console.error ('HR_REPORT_DATA_URL is not defined');
    return;
  }

  // Init DataTable
  const table = $ ('#listexpense').DataTable ({
    processing: true,
    serverSide: true,
    searching: false,
    deferLoading: 0, // ✅ ยังไม่โหลดจนกดค้นหา (ช่วยเร็ว)
    ajax: {
      url: HR_REPORT_DATA_URL,
      type: 'POST',
      headers: {
        'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content'),
      },
      data: function (d) {
        d.exdate = $ ('#exdate').val ();
        d.end_exdate = $ ('#end_exdate').val ();
        d.bu = $ ('#bu').val ();
        d.status = $ ('#status').val ();
      },
      error: function (xhr) {
        console.error ('DataTable AJAX error:', xhr.responseText);
      },
    },
    columns: [
      {data: 'company', defaultContent: '-'},
      {data: 'empid', defaultContent: '-'},
      {data: 'fullname', defaultContent: '-'},
      {data: 'dept', defaultContent: '-'},
      {data: 'grade', defaultContent: '-'},
      {data: 'bank', defaultContent: '-'},
      {data: 'bookid', defaultContent: '-'},
      {data: 'cartype', defaultContent: '-'},
      {data: 'passengers', defaultContent: 0},
      {data: 'person_type', defaultContent: '-'},
      {data: 'driver', defaultContent: '-'},
      {data: 'from', defaultContent: '-'},
      {data: 'to', defaultContent: '-'},
      {data: 'distance1', defaultContent: 0},
      {data: 'distance2', defaultContent: 0},
      {data: 'distance_total', defaultContent: 0},
      {data: 'exid', defaultContent: '-'},
      {data: 'startdate', defaultContent: '-'},
      {data: 'departuretime', defaultContent: '-'},
      {data: 'enddate', defaultContent: '-'},
      {data: 'returntime', defaultContent: '-'},
      {data: 'days', defaultContent: '-'},
      {data: 'food', defaultContent: 0},
      {data: 'gas', defaultContent: 0},
      {data: 'express', defaultContent: 0},
      {data: 'public', defaultContent: 0},
      {data: 'other', defaultContent: 0},
      {data: 'total', defaultContent: 0},
      {data: 'approve_type_text'},
      {data: 'approve_status_text'},

      {data: 'approve_cur', defaultContent: '-'},
      {data: 'approve_next', defaultContent: '-'},
      {data: 'remark', defaultContent: ''},
      {data: 'paymentdate', defaultContent: '-'},
    ],
  });

  // submit search
  $ ('form').on ('submit', function (e) {
    e.preventDefault ();

    const exdate = $ ('#exdate').val ();
    const end = $ ('#end_exdate').val ();

    if (!exdate || !end) {
      alert ('กรุณาเลือก Start Date และ End Date');
      return;
    }

    table.ajax.reload ();
  });
});

$ (document).on ('click', '#btnExport', function () {
  const exdate = $ ('#exdate').val ();
  const end_exdate = $ ('#end_exdate').val ();
  const bu = $ ('#bu').val ();
  const status = $ ('#status').val ();

  if (!exdate || !end_exdate) {
    alert ('กรุณาเลือกช่วงวันที่ก่อน Export');
    return;
  }

  const params = new URLSearchParams ({
    exdate: exdate,
    end_exdate: end_exdate,
    bu: bu || '',
    status: status || '',
  });

  const url = HR_REPORT_EXPORT_URL + '?' + params.toString ();
  window.open (url, '_blank');
});
