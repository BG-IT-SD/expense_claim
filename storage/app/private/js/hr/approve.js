$ (document).ready (function () {
  $ ('.appex').DataTable ({
    processing: true,
    order: [[2, 'desc']],
    // lengthMenu: [5, 10, 25, 50, 75, 100],
    paging: false,          // ไม่แบ่งหน้า
    info: false
  });

  // Click ปุ่มส่งข้อมูล
//   $ ('#sendSelected').on ('click', function () {
//     const csrfToken = document
//       .querySelector ('meta[name="csrf-token"]')
//       .getAttribute ('content');

//     const selected = $ ('.expense-checkbox:checked')
//       .map (function () {
//         return $ (this).val ();
//       })
//       .get ();

//     if (selected.length === 0) {
//       Swal.fire ({
//         icon: 'warning',
//         title: 'ยังไม่ได้เลือกรายการ',
//         text: 'กรุณาเลือกรายการอย่างน้อย 1 รายการ',
//       });
//       return;
//     }

//     // แสดง SweetAlert เพื่อยืนยัน
//     Swal.fire ({
//       title: 'ยืนยันการส่งข้อมูล?',
//       text: 'คุณต้องการส่งรายการที่เลือกไปยังหน้าถัดไปหรือไม่',
//       icon: 'question',
//       showCancelButton: true,
//       confirmButtonText: 'ใช่, ส่งเลย',
//       cancelButtonText: 'ยกเลิก',
//     }).then (result => {
//       if (result.isConfirmed) {
//         // สร้างฟอร์มแล้ว submit
//         let $form = $ ('<form>', {
//           action: hrNextApproveUrl,
//           method: 'POST',
//         });

//         $form.append (
//           $ ('<input>', {
//             type: 'hidden',
//             name: '_token',
//             value: csrfToken,
//           })
//         );

//         selected.forEach (function (id) {
//           $form.append (
//             $ ('<input>', {
//               type: 'hidden',
//               name: 'expense_ids[]',
//               value: id,
//             })
//           );
//         });

//         $ ('body').append ($form);
//         $form.submit ();
//       }
//     });
//   });



  // Check all
//   $ ('#selectAll').on ('click', function () {
//     $ ('.expense-checkbox').prop ('checked', this.checked);
//   });

//   // Auto-toggle selectAll
//   $ ('.expense-checkbox').on ('change', function () {
//     $ ('#selectAll').prop (
//       'checked',
//       $ ('.expense-checkbox:checked').length === $ ('.expense-checkbox').length
//     );
//   });

$('#sendSelected').on('click', function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    // หา tab ปัจจุบัน
    const activeTab = $('.nav-link.active').attr('data-bs-target'); // ex: #BG
    const plantName = activeTab.replace('#', '').trim();

    const plantNamenew = $('#selectedPlantName').val();
    const plantID = $('#plantID').val();

    // หา checkbox เฉพาะใน tab ที่ active (ใช้ data-plant)
    const selected = $(`.expense-checkbox[data-plant='${plantName}']:checked`).map(function () {
        return $(this).val();
    }).get();

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'ยังไม่ได้เลือกรายการ',
            text: 'กรุณาเลือกรายการอย่างน้อย 1 รายการ',
        });
        return;
    }

    Swal.fire({
        title: 'ยืนยันการส่งข้อมูล?',
        text: 'คุณต้องการส่งรายการที่เลือกไปยังหน้าถัดไปหรือไม่',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ส่งเลย',
        cancelButtonText: 'ยกเลิก',
    }).then(result => {
        if (result.isConfirmed) {
            let $form = $('<form>', {
                action: hrNextApproveUrl,
                method: 'POST',
            });

            $form.append(
                $('<input>', { type: 'hidden', name: '_token', value: csrfToken }),
               $('<input>', { type: 'hidden', name: 'plant_name', value: plantNamenew }),
                $('<input>', { type: 'hidden', name: 'plant_id', value: plantID })
            );

            selected.forEach(function (id) {
                $form.append(
                    $('<input>', {
                        type: 'hidden',
                        name: 'expense_ids[]',
                        value: id,
                    })
                );
            });

            $('body').append($form);
            $form.submit();
        }
    });
});


// Check all ในแต่ละ tab
$('.selectAll').on('click', function () {
    const plantName = $(this).data('plant');
    $(`.expense-checkbox[data-plant='${plantName}']`).prop('checked', this.checked).trigger('change');
});

// Auto-toggle selectAll ในแต่ละ tab
$('.expense-checkbox').on('change', function () {
    const plantName = $(this).data('plant');
    const all = $(`.expense-checkbox[data-plant='${plantName}']`);
    const checked = all.filter(':checked');
    $(`.selectAll[data-plant='${plantName}']`).prop('checked', all.length === checked.length);
});



$(function() {
    // หา tab ที่ active
    const $tab = $('.nav-link.active');
    if ($tab.length) {
        $('#selectedPlantName').val($tab.data('plantname'));
        $('#plantID').val($tab.data('plantid'));
    }
});

$('a[data-bs-toggle="tab"], button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    // ดึง id ของ tab-pane ที่ active
    const targetPane = $(e.target).attr('data-bs-target');
    if (!targetPane) return;

    // ชื่อ plantName
    const plantName = targetPane.replace('#', '').trim();

    // เคลียร์ checkbox เฉพาะใน tab นี้
    $(`.expense-checkbox[data-plant='${plantName}']`).prop('checked', false);

    // เคลียร์ selectAll tab นี้ด้วย
    $(`.selectAll[data-plant='${plantName}']`).prop('checked', false);

    const plantNameData = $(e.target).data('plantname');
    const plantID   = $(e.target).data('plantid');
    $('#selectedPlantName').val(plantNameData); // keep plant name only current tab
    $('#plantID').val(plantID);


});


$('#confrimapprove').on('click', function (e) {
    e.preventDefault();

    const formData = $('#frmSendGroupApprove').serialize();

    Swal.fire({
        title: 'ยืนยันการส่งข้อมูลอนุมัติ?',
        text: 'คุณต้องการส่งข้อมูลนี้เพื่ออนุมัติในขั้นถัดไปหรือไม่',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ส่งเลย',
        cancelButtonText: 'ยกเลิก',
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: hrHeadApproveUrl,
                method: 'POST',
                data: formData,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function (xhr) {
                    const res = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: res?.message || 'ไม่สามารถดำเนินการได้',
                    });
                }
            });
        }
    });
});


// 1) กดปุ่ม REJECT ในตาราง -> เปิด modal และเซ็ตค่า hidden
    $(document).on('click', '.btn-open-reject', function () {
        const expenseId      = $(this).data('expense-id');
        const empemail       = $(this).data('empemail') || '';
        const empfullname    = $(this).data('empfullname') || '';
        const departuredaterj= $(this).data('departuredaterj') || '';

        $('#rejectidexpense').val(expenseId);
        $('#empemailrj').val(empemail);
        $('#empfullname').val(empfullname);
        $('#departuredaterj').val(departuredaterj);

        $('#rejectremark').val('');   // เคลียร์ข้อความเดิม
        $('#popUpReject').modal('show');
    });

    // 2) ปุ่มยืนยัน Reject (โค้ดของคุณเดิม + เพิ่ม validate)
    $('.btnreject').on('click', function (e) {
        e.preventDefault();

        let rejectremark = $('#rejectremark').val().trim();
        if (!rejectremark) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณากรอกเหตุผลที่ไม่อนุมัติ',
            });
            return;
        }

        let expenseId   = $('#rejectidexpense').val();
        let headEmail   = $('#head_emailrj').val();
        let headName    = $('#head_namerj').val();
        let headId      = $('#head_idrj').val();
        let departuredaterj = $('#departuredaterj').val();
        let empemailrj  = $('#empemailrj').val();
        let empfullname = $('#empfullname').val();

        $.ajax({
            url: '/HR/reject',
            type: 'POST',
            data: {
                rejectremark: rejectremark,
                rejectidexpense: expenseId,
                head_emailrj: headEmail,
                head_namerj: headName,
                head_idrj: headId,
                departuredaterj: departuredaterj,
                empemailrj: empemailrj,
                empfullname: empfullname,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.fire({
                    icon: response.class,
                    title: response.message,
                }).then(() => {
                    window.location.href = "/HR";
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: xhr.responseJSON?.message || 'ไม่สามารถบันทึกข้อมูลได้',
                });
            }
        });
    });


});
