function updateMealTotal () {
  // คำนวณเฉพาะแถวที่มี .meal-total (คือแถวเบิกมื้ออาหาร)
  $ ('.meal-total').each (function () {
    const $row = $ (this).closest ('tr');
    let total = 0;

    $row.find ('.meal-checkbox').each (function () {
      if ($ (this).is (':checked')) {
        total += parseFloat ($ (this).data ('price')) || 0;
      }
    });

    $row.find ('.meal-total').text (total.toFixed (2));
    //หา .meal-day-box แล้วอัปเดต totalpricebf ที่อยู่ใน tr.sumallday
    const $dayBox = $row.closest ('.meal-day-box');
    $dayBox.find ('.totalpricebf').val (total.toFixed (2));
  });
}

function updateCompanyMealTotal (day) {
  let total = 0;
  let $dayBox = null;

  $ ('.mealx-checkbox[data-day="' + day + '"]').each (function () {
    const isChecked = $ (this).is (':checked');
    const price = isChecked ? parseFloat ($ (this).data ('price')) || 0 : 0;
    total += price;

    // กำหนด $dayBox ครั้งแรก
    if (!$dayBox) {
      $dayBox = $ (this).closest ('.meal-day-box');
    }
  });

  $ ('.totalxmealcount[data-day="' + day + '"]').text (total.toFixed (2));

  if ($dayBox) {
    $dayBox.find ('.totalreject').val (total.toFixed (2));
  }
}

function updateDayMealBox(level = 0) {
    $('.meal-day-box').each(function () {
      const $box = $(this);
      let totalPerMeal = [0, 0, 0, 0]; // มื้อเช้า กลางวัน เย็น ดึก
      let checkedCount = 0;

      // 1. รวมจาก .meal-checkbox ที่ checked
      $box.find('.meal-checkbox').each(function (index) {
        let isChecked = $(this).is(':checked');
        let price = isChecked ? parseFloat($(this).data('price')) || 0 : 0;
        let mealIndex = index % 4;

        if (isChecked) {
          checkedCount++;
          // ✅ ถ้า level > 7 และเลือกเกิน 3 มื้อ → ยกเลิก checkbox นี้
          if (level > 7 && checkedCount > 3) {
            $(this).prop('checked', false);
            price = 0;
          }
        }

        totalPerMeal[mealIndex] += price;
      });

      // 2. ลบออกถ้า .mealx-checkbox ถูกติ๊ก
      $box.find('.mealx-checkbox').each(function (index) {
        let isChecked = $(this).is(':checked');
        let price = isChecked ? parseFloat($(this).data('price')) || 0 : 0;
        let mealIndex = index % 4;
        if (isChecked) {
          totalPerMeal[mealIndex] -= price;
        }
      });

      // 3. แสดงใน <tr class="sumallday">
      const $summaryRow = $box.find('tr.sumallday td');
      for (let i = 0; i < 4; i++) {
        let val = totalPerMeal[i] < 0 ? 0 : totalPerMeal[i];
        $summaryRow.eq(i + 1).text(val.toFixed(2));
      }

      // 4. รวมทั้งหมด
      let totalAll = totalPerMeal.reduce((sum, val) => sum + (val < 0 ? 0 : val), 0);
      $summaryRow.eq(5).text(totalAll.toFixed(2));

      // ✅ 5. ใส่ลงใน input.totalprice
      $box.find('.totalprice').val(totalAll.toFixed(2));
    });
  }


function updateGrandTotal () {
  let total = 0;

  $ ('.meal-day-box').each (function () {
    const $box = $ (this);
    const sumText = $box.find ('tr.sumallday td').last ().text ();
    //   const sumTextbf = $box.find('.meal-total').text();
    const sum = parseFloat (sumText) || 0;
    //   const sumbf = parseFloat(sumTextbf) || 0;
    total += sum;
    //   totalbf += sumbf;
  });

  $ ('.grandTotal').text (total.toFixed (2));
  $ ('#costoffood').val (total.toFixed (2));
  $ ('.totallastfood').text (total.toFixed (2));
  //   $ ('#totalprice').val (total.toFixed (2));
  calculateTotalExpense ();
}

function calculateTravelTotal () {
  let publicFare = parseFloat ($ ('#publictransportfare').val ()) || 0;
  let expressToll = parseFloat ($ ('#expresswaytoll').val ()) || 0;
  let other = parseFloat ($ ('#otherexpenses').val ()) || 0;

  let total = publicFare + expressToll + other;

  $ ('.totaltravel').text (total.toFixed (2));
  $ ('#travelexpenses').val (total.toFixed (2));

  calculateTotalExpense ();
}

function calculateTotalExpense () {
  let total = 0;

  $ ('.expense-value').each (function () {
    let value = parseFloat ($ (this).val ()) || 0;
    total += value;
  });


  $ ('#totalExpense').val (total.toFixed (2));
  $ ('.totalExpense').text (total.toFixed (2));
}

$ (document).ready (function () {
    var level = $("#empleveldata").val();
    $("#popUpExpense").modal('show');
    const seenDays = new Set();

//   $ ('#departurefrom').change (function () {
//     if ($ (this).val () == '2') {
//       $ ('#MapDistanceModal').show ();
//     }
//   });

  // ถึงเวลา
  $ ('#returntime').timepicker ({
    show: '24:00',
    timeFormat: 'H:i:s',
    orientation: isRtl ? 'r' : 'l',
  });

  // เรียกเมื่อโหลด
  updateMealTotal ();
  updateDayMealBox (level);
  updateGrandTotal ();
  calculateTotalExpense ();

  // เมื่อเช็คช่องบริษัทฯ → คำนวณใหม่
  $ ('.mealx-checkbox').on ('change', function () {
    const day = $ (this).data ('day');
    updateCompanyMealTotal (day);
    updateDayMealBox (level); // เพิ่มตรงนี้เพื่อคำนวณใหม่หลังติ๊กบริษัท
    updateGrandTotal ();
    if (!seenDays.has(day)) {
      seenDays.add(day);
      updateCompanyMealTotal(day); // ✅ เรียกตอนโหลดหน้า
    }
  });

  //ผลรวมของค่าใช้จ่ายอื่นๆ
  $ ('#publictransportfare, #expresswaytoll, #otherexpenses').on (
    'keyup change',
    calculateTravelTotal
  );
  // Approve
  $ ('#headapprove').select2 ({
    ajax: {
      url: '/Expense/Heademp',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          sKeyword: params.term,
          page: params.page || 1,
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

  $ ('#headapprove').change (function () {
    var id = $ (this).val ();
    $.ajax ({
      url: '/Expense/GetAllHeadEmp',
      type: 'get',
      data: 'emid=' + id,
      dataType: 'json',
      success: function (data) {
        console.log (data);
        $ ('#head_email').val (data.Emailemp);
        $ ('#head_name').val (data.Nameemp);
        $ ('#head_id').val (data.Idemp);
      },
      error: function (data) {},
    });
  });

//upload file
$('#add-file').on('click', function () {
    const row = `
      <div class="file-row mb-2 d-flex gap-2 align-items-center">
        <input type="file" name="files[]" class="form-control w-75" required>
        <button type="button" class="btn btn-danger btn-remove">ลบ</button>
      </div>`;
    $('#file-container').append(row);
  });

  // ลบแถว input
  $('#file-container').on('click', '.btn-remove', function () {
    $(this).closest('.file-row').remove();
  });

//   Reject
$("#rejectbtn").on('click', function () {
    $("#popUpReject").modal('show');
});

$('.btnreject').on('click', function (e) {
    e.preventDefault();

    let form = $('#rejectfrm');
    let rejectremark = $('#rejectremark').val();
    let expenseId = $('#rejectidexpense').val();
    let headEmail = $('#head_emailrj').val();
    let headName = $('#head_namerj').val();
    let headId = $('#head_idrj').val();

    let departuredaterj = $('#departuredaterj').val();
    let empemailrj = $('#empemailrj').val();
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

// น้ำมัน
  // ฟังก์ชันเปลี่ยนการแสดงผลตาม radio ที่เลือก


$('input[name="checktoil"]').on('change', function () {
    let totaldistance = parseFloat($("#totaldistance").val()) || 0;
    let totaldistancemax = parseFloat($("#totaldistancemax").val()) || 0;
    let distancemore = parseFloat($('#distancemore').val()) || 0;
    let bath = parseFloat($("#bath_per_km").val()) || 0;

    let baseDistance = 0;
    let finalDistance = 0;

    if (distancemore > 0) {
        $('#base-distance-distance-label').show();
        if (totaldistance > totaldistancemax && totaldistancemax > 0) {
            baseDistance = totaldistancemax;
            finalDistance = totaldistancemax + distancemore;
        } else {
            baseDistance = totaldistance;
            finalDistance = totaldistance + distancemore;
        }
    } else {
        if (totaldistance > totaldistancemax && totaldistancemax > 0) {
            baseDistance = totaldistancemax;
            finalDistance = totaldistancemax;
        } else {
            baseDistance = totaldistance;
            finalDistance = totaldistance;
        }
    }

    let basePrice = (baseDistance * bath).toFixed(2);
    let totalPrice = (finalDistance * bath).toFixed(2);

    if ($(this).val() == "1") {
        $('#rateoil').show();
        $('#allsumoil').show();

        $(".lastkm").text(finalDistance.toFixed(2));
        $("#afdistance").val(finalDistance.toFixed(2));
        $('#base_distance').val(baseDistance.toFixed(2)); // เก็บ base distance
        $('#base-distance-distance-label').text('ระยะก่อนเพิ่มเติม : ' + baseDistance.toFixed(2));

        $(".pricesuccess").text(totalPrice);
        $(".gasolinecost").text(totalPrice);
        $('#gasolinecost').val(totalPrice);

    } else {
        $('#rateoil').hide();
        $('#allsumoil').hide();

        $(".lastkm").text("0");
        $(".pricesuccess").text("0.00");
        $(".gasolinecost").text("0.00");
        $('#gasolinecost').val("0");
        $('#distancemore').val("0");
        $("#afdistance").val("0");
        $('#base_distance').val("0"); // รีเซ็ต base
        $('#base-distance-distance-label').text(0);
        $('#base-distance-distance-label').hide();
    }

    calculateTotalExpense();
});

$('#distancemore').on('input', function () {
    $('input[name="checktoil"]:checked').trigger('change');
});


// ตรวจสอบตอนโหลดหน้าว่าค่าไหนถูกเลือก
// if ($('input[name="checktoil"]:checked').val() == "1") {
//     $('#rateoil').show();
//     $('#allsumoil').show();
// }

if ($('input[name="checktoil"]:checked').val() == "1") {
    $('input[name="checktoil"]:checked').trigger('change');
}

});


