function recalculateTotals () {
  let sumFood = 0;
  let sumGas = 0;
  let sumExpress = 0;
  let sumPublic = 0;
  let sumOther = 0;
  let sumTotal = 0;

  $ ('.action-select').each (function () {
    const rowId = $ (this).data ('id');
    const action = $ (this).val ();

    if (action === '1') {
      const food = parseFloat ($ (`.row-food[data-id="${rowId}"]`).val ()) || 0;
      const gas = parseFloat ($ (`.row-gas[data-id="${rowId}"]`).val ()) || 0;
      const express =
        parseFloat ($ (`.row-express[data-id="${rowId}"]`).val ()) || 0;
      const publict =
        parseFloat ($ (`.row-public[data-id="${rowId}"]`).val ()) || 0;
      const other =
        parseFloat ($ (`.row-other[data-id="${rowId}"]`).val ()) || 0;

      const totalOther = express + publict + other;
      const total = food + gas + totalOther;

      sumFood += food;
      sumGas += gas;
      sumExpress += express;
      sumPublic += publict;
      sumOther += other;
      sumTotal += total;
    }
  });

  const sumTravel = sumExpress + sumPublic + sumOther;

  //อัปเดต TEXT แสดงผล
  $ ('.sum-mealnet').text (
    sumFood.toLocaleString (undefined, {
      minimumFractionDigits: 2,
    })
  );
  $ ('.gasolinecostnet').text (
    sumGas.toLocaleString (undefined, {
      minimumFractionDigits: 2,
    })
  );
  $ ('.totaltravelnet').text (
    sumTravel.toLocaleString (undefined, {
      minimumFractionDigits: 2,
    })
  );
//   $ ('.totalExpenseNet').text (
//     sumTotal.toLocaleString (undefined, {
//       minimumFractionDigits: 2,
//     })
//   );
$('.totalExpenseNet').text(
    Math.round(sumTotal).toLocaleString(undefined, {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    })
  );

  $ ('.row-foodnet').val (sumFood.toFixed (2));
  $ ('.row-gasnet').val (sumGas.toFixed (2));
  $ ('.row-othernet').val (sumTravel.toFixed (2));
//   $ ('.row-totalexNet').val (sumTotal.toFixed (2));
    let roundedInt = Math.round(sumTotal);
    $('.row-totalexNet').val(roundedInt);

  $ ('.row-sum_express').val (sumExpress.toFixed (2));
  $ ('.row-sum_publictransport').val (sumPublic.toFixed (2));
  $ ('.row-sum_other').val (sumOther.toFixed (2));
}

$ (document).ready (function () {
  $ ('#paymentdate').flatpickr ({
    defaultDate: "today",
    monthSelectorType: 'static',
  });

  $ ('.status-dropdown').select2 ({
    templateResult: function (data) {
      if (!data.id) return data.text;

      var $result = $ ('<span></span>');
      $result.text (data.text);

      if ($ (data.element).data ('color')) {
        $result.addClass (
          $ (data.element).data ('color') + ' text-white px-2 rounded'
        );
      }

      return $result;
    },
    templateSelection: function (data) {
      if (!data.id) return data.text;

      var $result = $ ('<span></span>');
      $result.text (data.text);

      if ($ (data.element).data ('color')) {
        $result.addClass (
          $ (data.element).data ('color') + ' text-white px-2 rounded'
        );
      }

      return $result;
    },
  });

  $ ('[data-bs-toggle="tooltip"]').tooltip (); // Init tooltip

  $ ('.action-select').on ('change', function () {
    const action = $ (this).val ();
    const rowId = $ (this).data ('id');
    const $select = $ (this);

    if (action === '9' || action === '2') {
      const label = action === '9' ? 'Hold' : 'Reject';

      Swal.fire ({
        title: 'กรุณากรอกเหตุผล',
        input: 'textarea',
        inputLabel: 'เหตุผลที่เลือก ' + label,
        inputPlaceholder: 'พิมพ์เหตุผลที่นี่...',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        inputValidator: value => {
          if (!value) return 'กรุณากรอกเหตุผลก่อน!';
        },
      }).then (result => {
        if (result.isConfirmed) {
          const reason = result.value;

          const $rowBox = $ ('.reasonarea[data-id="' + rowId + '"]');
          const $input = $rowBox.find ('.txtreason');
          const $btn = $rowBox.find ('button');

          $input.val (reason);
          $btn
            .attr ('title', reason)
            .attr ('data-bs-original-title', reason)
            .tooltip ('dispose')
            .tooltip ();
          $rowBox.show ();

          recalculateTotals (); // ✅ อัปเดตยอด
        } else {
          $select.val ('1').trigger ('change'); // กลับไป Approve
          recalculateTotals (); // ✅ อัปเดตยอด
        }
      });
    } else {
      $select.val ('1').trigger ('change'); // กลับไป Approve
      recalculateTotals (); //อัปเดตยอดกรณีเปลี่ยนกลับ Approve
    }
  });

  $ ('#approveForm').on ('submit', function (e) {
    e.preventDefault (); //ยกเลิก submit ปกติ

    Swal.fire ({
      title: 'คุณแน่ใจหรือไม่?',
      text: 'คุณต้องการบันทึกผลการอนุมัติใช่หรือไม่?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'ใช่, บันทึกเลย',
      cancelButtonText: 'ยกเลิก',
    }).then (result => {
      if (result.isConfirmed) {
        //ส่ง form จริงหลังจาก confirm
        this.submit ();
      }
    });
  });
});
