// ===============================
// State
// ===============================
let lastEmpid = null;

// ===============================
// Timepicker (24h)
// ===============================
function initTimepicker() {
  $('#inputStart, #inputEnd').timepicker('remove');
  $('#inputStart, #inputEnd').timepicker({
    timeFormat: 'H:i',
    step: 15,
    scrollDefault: 'now',
  });
}

// ===============================
// Load booking list (AJAX)
// ===============================
function loadBookingList(empid) {
  if (!empid) return;

  lastEmpid = empid;

  $.ajax({
    url: '/DriverClaim/search-booking',
    method: 'GET',
    data: { empid },
  })
    .done(function (html) {
      $('#resultArea').html(html);
    })
    .fail(function (xhr) {
      Swal.fire({
        icon: 'error',
        title: 'โหลดข้อมูลไม่สำเร็จ',
        text: xhr.responseJSON?.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
      });
    });
}

// ===============================
// Ready
// ===============================
$(document).ready(function () {
  $('#drivers').select2();

  // Search (AJAX)
  $('#searchForm').on('submit', function (e) {
    e.preventDefault();

    const empid = $('#drivers').val();
    if (!empid) {
      Swal.fire({ icon: 'warning', title: 'กรุณาเลือก พขร.' });
      return;
    }

    loadBookingList(empid);
  });

  // (optional) กัน aria-hidden warning
  const modalEl = document.getElementById('modalEditBookingTime');
  if (modalEl) {
    modalEl.addEventListener('hide.bs.modal', () => {
      if (modalEl.contains(document.activeElement)) document.activeElement.blur();
    });
  }
});

// ===============================
// Open modal edit time (delegation)
// ===============================
$(document).on('click', '.btnEditBookingTime', function () {
  const $btn = $(this);

  $('#mBookId').text($btn.data('bookid'));
  $('#mDate').text($btn.data('date') || '');
  $('#mLocation').text($btn.data('location') || '');

  $('#inputBookId').val($btn.data('bookid'));
  $('#inputStart').val($btn.data('start') || '');
  $('#inputEnd').val($btn.data('end') || '');

  // ต้องมี data-action ในปุ่ม
  const actionUrl = $btn.data('action');
  if (!actionUrl) {
    Swal.fire({ icon: 'error', title: 'ไม่พบ URL', text: 'กรุณาใส่ data-action ที่ปุ่ม' });
    return;
  }
  $('#formEditBookingTime').attr('action', actionUrl);

  initTimepicker();

  bootstrap.Modal.getOrCreateInstance(
    document.getElementById('modalEditBookingTime')
  ).show();
});

// ===============================
// Save edit time (AJAX)
// ===============================
$(document).on('submit', '#formEditBookingTime', function (e) {
  e.preventDefault();

  const $form = $(this);
  const url = $form.attr('action');

  const $submitBtn = $form.find('button[type="submit"]');
  $submitBtn.prop('disabled', true);

  if (!url) {
    Swal.fire({ icon: 'error', title: 'ไม่พบ action ของฟอร์ม' });
    $submitBtn.prop('disabled', false);
    return;
  }

  $.ajax({
    url: url,
    method: 'POST',
    data: {
      _token: $form.find('input[name="_token"]').val(),
      _method: 'PUT',
      start_time: $('#inputStart').val(),
      end_time: $('#inputEnd').val(),
    },
    dataType: 'json',
    headers: { Accept: 'application/json' },
  })
    .done(function (res) {
      const modalEl = document.getElementById('modalEditBookingTime');
      bootstrap.Modal.getOrCreateInstance(modalEl).hide();

      Swal.fire({
        icon: 'success',
        title: res?.message || 'บันทึกเรียบร้อย',
        timer: 1200,
        showConfirmButton: false,
      });

      // ✅ รีเฟรชตารางด้วย empid เดิม (ไม่รีหน้า)
      if (lastEmpid) loadBookingList(lastEmpid);
    })
    .fail(function (xhr) {
      let msg = xhr.responseJSON?.message || 'บันทึกไม่สำเร็จ';
      if (xhr.status === 422 && xhr.responseJSON?.errors) {
        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
        msg = xhr.responseJSON.errors[firstKey][0] || msg;
      }

      Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: msg });
    })
    .always(function () {
      $submitBtn.prop('disabled', false);
    });
});
