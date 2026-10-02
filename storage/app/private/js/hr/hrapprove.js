$(document).on('click', '.btn-passenger', function() {
    let bookid = $(this).data('bookid');

    $.get(`/HR/passenger-list/${bookid}`, function(res) {
        let html = '';
        res.forEach((item) => {
            let expenseId = item.expense?.id ?? '-';
            let empid = item.passenger_empid;
            let name = item.passenger_name;

            let last = item.expense?.lastapprove ?? null;
            let statusText = last?.status_text ?? '-';
            let typeText = last?.type_text ?? '-';

            let actionBtn = '';
            let viewUrl = `/HR/view/${expenseId}/0`; // expenseId มาจาก item.expense?.id
            let ApproveUrl = `/HR/edit/${expenseId}`;

            if (last) {
                const type = parseInt(last.typeapprove);
                const status = parseInt(last.statusapprove);

                // เงื่อนไขแสดงปุ่ม Approve
                if ((type === 1 && (status === 1)) || (type === 3 &&
                        status === 0)) {
                    actionBtn = `<a href="${ApproveUrl}" target="_blank" class="btn btn-sm btn-warning">
                    <span class="mdi mdi-eye-circle-outline"></span> ตรวจสอบ
                 </a>`;
                }

                // เงื่อนไขแสดงปุ่ม View
                if ((type === 3 && status === 2) || (type === 1 && status === 0) || type >
                    3) {
                    actionBtn = `<a href="${viewUrl}" class="btn btn-sm btn-info">
            <span class="mdi mdi-eye-arrow-right-outline"></span> View
         </a>`;
                }

                // เงื่อนไขแสดงปุ่ม View
                if (status === 2) {
                    actionBtn = `<a href="${viewUrl}" class="btn btn-sm btn-info">
            <span class="mdi mdi-eye-arrow-right-outline"></span> View
         </a>`;
                }
            }

            html += `
    <tr>
        <td>${expenseId}</td>
        <td>${empid}</td>
        <td>${name}</td>
        <td>${typeText}</td>
        <td>${statusText}</td>
        <td>${actionBtn}</td>
    </tr>`;
        });

        $('#passenger-table-body').html(html);
        $('#modalGroup').modal('show');
    });

});



$ (document).ready (function () {
    $ ('#historydriver').DataTable ({
      processing: true,
      order: [[2, 'desc']],
    //   lengthMenu: [5, 10, 25, 50, 75, 100],
    });

    $ ('#listexpense').DataTable ({
        processing: true,
        order: [[2, 'desc']],
        // lengthMenu: [5, 10, 25, 50, 75, 100],
      });

      $ ('#expensedvhr').DataTable ({
        processing: true,
        order: [[2, 'desc']],
        // lengthMenu: [5, 10, 25, 50, 75, 100],
      });

      $ ('#exdate').flatpickr ({
        monthSelectorType: 'static',
      });

      $ ('#end_exdate').flatpickr ({
        monthSelectorType: 'static',
      });

      $('#drivers').select2();


  });
