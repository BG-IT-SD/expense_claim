// ฟังก์ชัน format จำนวนเต็ม
function formatInt(val) {
  return Math.round(val); // ✅ ปัดเศษเป็นจำนวนเต็ม
}

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

function updateHRMealTotal (day) {
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

    $ ('.meal-total[data-day="' + day + '"]').text (total.toFixed (2));

    if ($dayBox) {
      $dayBox.find ('.totalprice').val (total.toFixed (2));
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

  $('.meal-day-box').each(function () {
    const $box = $(this);
    const sumText = $box.find('tr.sumallday td').last().text();
    const sum = parseFloat(sumText) || 0;
    total += sum;
  });

  const roundedTotal = Math.round(total);

  // ✅ แสดงผลรวมเป็นจำนวนเต็มแต่บังคับ .00
  $('.grandTotal').text(roundedTotal.toFixed(2));
  $('#costoffood').val(roundedTotal);
  $('.totallastfood').text(roundedTotal.toFixed(2));

  calculateTotalExpense();
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

  $('.expense-value').each(function () {
    let value = parseFloat($(this).val()) || 0;
    total += value;
  });

  const roundedTotal = Math.round(total);

  // ✅ แสดงผลรวมเป็นจำนวนเต็มแต่บังคับ .00
  $('#totalExpense').val(roundedTotal);
  $('.totalExpense').text(roundedTotal.toFixed(2));
  }

 // 1) ฟังก์ชันหลัก: จัดการซ่อน/แสดง และเซ็ตระยะทาง
function applyPlantToPlantRule({ recalcOil = false } = {}) {
  const depFrom = parseInt($('#departurefrom').val()) || 0; // 1=บริษัท, 2=สถานที่พัก
  const retFrom = parseInt($('#returnfrom').val()) || 0;
  const bothCompany = depFrom === 1 && retFrom === 1;

  if (bothCompany) {
    // ซ่อนแผนที่ และใช้ max แทน
    if ($('#MapDistanceModal').length) $('#MapDistanceModal').hide();
    if ($('#map').length) $('#map').hide();
    $('.mapsearch').hide();

    const tmax = parseFloat($('#totaldistancemax').val()) || 0;
    $('#totaldistance').val(tmax.toFixed(2));
    $('#totaldistance_text').val(tmax.toFixed(2));

    // เคลียร์ช่องค้นหา/พิกัด
    $('#origin, #destination').val('');
    $('#distance').text('');
    $('#latitude, #longitude, #latitude_b, #longitude_b, #map_a_name, #map_b_name').val('');
  } else {
    // มีฝั่งใดเป็น "ที่พัก" → แสดงแผนที่ และคืนค่า distance
    $('.mapsearch').show();
    if ($('#map').length) $('#map').show();

    if (depFrom === 2 || retFrom === 2) {
    //   $('#totaldistance').val(0);
    //   $('#totaldistance_text').val(0);
    }
  }

  // Reset checktoil และค่าน้ำมันทุกครั้งที่เปลี่ยน departure/return
//   $('input[name="checktoil"]').prop('checked', false); // ยกเลิกการเลือกทั้งหมด
// ตั้งให้ default เป็น "ไม่ประสงค์เบิกน้ำมัน"
  $('input[name="checktoil"][value="2"]').prop('checked', true);
  $('#rateoil, #allsumoil').hide();
  $(".lastkm").text("0");
  $(".pricesuccess, .gasolinecost").text("0.00");
  $('#gasolinecost').val("0");
  $('#distancemore').val("0");
  $("#afdistance").val("0");
  $('#base_distance').val("0");
  $('#base-distance-distance-label').text(0).hide();

  // คำนวณรวมใหม่หลังรีเซ็ต
  calculateTotalExpense();
}

// เงื่อนไขมื้ออาหารใหม่ 19.09.25
// ปรับเวลารูปแบบ HH:mm จาก H:i หรือ H:i:s
    function normalizeHHmm(val) {
    if (!val) return null;
    const m = String(val).trim().match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);
    if (!m) return null;
    let hh = +m[1], mm = +m[2];
    if (hh < 0 || hh > 23 || mm < 0 || mm > 59) return null;
    return String(hh).padStart(2,'0') + ':' + String(mm).padStart(2,'0');
    }

    // ฟังก์ชันใหม่: ใช้เวลา start / return ไปอัปเดตมื้ออาหารทุกวัน
    function applyMealsByTime(hhmmReturn) {
    $('.meal-day-box').each(function() {
        const $box = $(this);
        const isLastDay = $box.hasClass('is-last-day'); // วันสุดท้าย
        const fromSpan = $box.find('.from-time');
        const toSpan   = $box.find('.to-time');

        let fromHour = 0;
        if (fromSpan.length) {
        const [fH, fM] = fromSpan.text().split(':').map(Number);
        fromHour = fH + (fM > 0 ? 0.01 : 0);
        }

        let toHour = 23.99; // ค่า default (วันกลาง)
        if (isLastDay && hhmmReturn) {
        const [H, M] = hhmmReturn.split(':').map(Number);
        toHour = H + (M > 0 ? 0.01 : 0);
        if (toSpan.length) toSpan.text(hhmmReturn); // อัปเดต label
        }

        // เช็คบ็อกซ์ของวันนั้น
        const checks = $box.find('tbody tr:first input.meal-checkbox');
        checks.each((i, el) => el.onclick = null); // ปลดล็อกก่อน

        //  กติกาใหม่
        if (checks[0]) checks[0].checked = (fromHour < 8 || (toHour > 6 && fromHour < 8));                 // มื้อเช้า
        if (checks[1]) checks[1].checked = (fromHour < 17 && toHour > 8);  // กลางวัน
        if (checks[2]) checks[2].checked = (fromHour < 23 && toHour > 17); // เย็น
        if (checks[3]) checks[3].checked = (toHour > 21);                  // ดึก

        checks.each((i, el) => el.onclick = function(){ return false; }); // ล็อกกลับ
    });

    // อัปเดตยอดรวม
    if (typeof updateMealTotal === 'function') updateMealTotal();
    const level = parseInt($('#empleveldata').val() || '0', 10);
    if (typeof updateDayMealBox === 'function') updateDayMealBox(level);
    if (typeof updateGrandTotal === 'function') updateGrandTotal();
    if (typeof calculateTotalExpense === 'function') calculateTotalExpense();
    }

    // เรียกเมื่อ returntime เปลี่ยน
    function syncReturnTime() {
    const raw = $('#returntime').val();
    const hhmm = normalizeHHmm(raw);
    applyMealsByTime(hhmm);
    }


$ (document).ready (function () {
   // เรียก syncReturnTime เฉพาะกรณี create (page_mode != 1)
    const mode = $("#page_mode").val();
    if ($.inArray(mode, ["0", "1", "3"]) === -1) {
    syncReturnTime();
    }
    // อัปเดตเมื่อผู้ใช้แก้ไข (อนุญาตให้คำนวณใหม่เสมอถ้ามีการเปลี่ยนเวลา)
    $('#returntime').on('change blur', function () {
            syncReturnTime();
    });

    var level = $("#empleveldata").val();
    $("#popUpExpense").modal('show');
    const seenDays = new Set();



  // ถึงเวลา
  $ ('#returntime').timepicker ({
    show: '24:00',
    timeFormat: 'H:i:s',
    orientation: (typeof isRtl !== 'undefined' && isRtl) ? 'r' : 'l',
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

  $ ('.meal-checkbox').on ('change', function () {
    const day = $ (this).data ('day');
    updateCompanyMealTotal (day);
    updateHRMealTotal (day);
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
//   $ ('#headapprove').select2 ({
//     ajax: {
//       url: '/Expense/Heademp',
//       dataType: 'json',
//       delay: 250,
//       data: function (params) {
//         return {
//           sKeyword: params.term,
//           page: params.page || 1,
//         };
//       },
//       processResults: function (data, params) {
//         params.page = params.page || 1;
//         return {
//           results: data.data,
//           pagination: {
//             more: params.page * 5 < data.total_count,
//           },
//         };
//       },
//       cache: true,
//     },
//     minimumInputLength: 1,

//     //เพิ่มตรงนี้เพื่อให้ตอนเลือกแล้ว แสดง email | ชื่อ
//     templateSelection: function (data) {
//       if (data.text) {
//         return data.text;
//       }
//       return data.email + ' | ' + data.name; // fallback ถ้าจัดแยกไว้
//     },
//   });

$('#headapprove').select2({
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
    const grouped = {};

    // แบ่งกลุ่มก่อน (ตาม group ที่ส่งจาก PHP)
    data.data.forEach(item => {
        if (!grouped[item.group]) grouped[item.group] = [];
        grouped[item.group].push({ id: item.id, text: item.text });
    });

    // แปลงเป็นรูปแบบที่ select2 ใช้ได้
    const formatted = Object.keys(grouped).map(group => ({
        text: group,
        children: grouped[group],
    }));

    return { results: formatted };
    },
    },
    placeholder: 'ค้นหาผู้อนุมัติ...',
    minimumInputLength: 1,
    width: '100%',
});


  $ ('#headapprove').change (function () {
    var id = $ (this).val ();
    $.ajax ({
      url: '/Expense/GetAllHeadEmp',
      type: 'get',
      data: 'emid=' + id,
      dataType: 'json',
      success: function (data) {
        // console.log (data);
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

    // applyPlantToPlantRule(); // sync ระยะทาง/การซ่อนแผนที่ตามเงื่อนไขล่าสุด
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

       //ปัดเศษราคาน้ำมันเป็นจำนวนเต็มตามหลักคณิตศาสตร์
        const roundedOilCost = Math.round(totalPrice);

        // แสดงผลแบบมี .00 เพื่อความสวยงาม
        $(".pricesuccess").text(roundedOilCost.toFixed(2));
        $(".gasolinecost").text(roundedOilCost.toFixed(2));
        $('#gasolinecost').val(roundedOilCost);
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

// New check ค่าเดินทางใหม่กรณี plant to plant
if ($("#page_mode").val() == 99) {
  applyPlantToPlantRule({ recalcOil: true });
}


  // 3) เมื่อผู้ใช้เปลี่ยนค่า "ออกเดินทางจาก" / "สถานที่เดินทางกลับ"
  $('#departurefrom, #returnfrom').on('change', function () {
    applyPlantToPlantRule({ recalcOil: true });
  });

if ($("#page_mode").val() == 1) {
  console.log("HR mode: enable manual distance edit");

  $("#totaldistance_text")
    .prop("readonly", false)
    .prop("disabled", false)
    .addClass("border-primary");

  $("#hr-distance-hint").show();

  // เมื่อแก้ไขระยะทาง → sync hidden field + คำนวณค่าน้ำมันใหม่
  $("#totaldistance_text").on("input change", function () {
    const val = parseFloat($(this).val()) || 0;
    $("#totaldistance").val(val.toFixed(2));

    // ถ้ามีการคำนวณค่าน้ำมันอยู่ → trigger ให้คำนวณใหม่
    $(".lastkm").text(val.toFixed(2));
    $('input[name="checktoil"]:checked').trigger("change");
  });
}



});


