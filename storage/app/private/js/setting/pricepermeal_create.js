$ (document).ready (function () {

    // Save Main
  $ ('#saveButton').click (function () {
    $ ('#frmPricepermeal').submit (); // ✅ สั่งให้ฟอร์มส่งข้อมูล

    // let formData = $ ('#frmPricepermeal').serialize ();

    // console.log ('ส่งข้อมูล:', formData); // ตรวจสอบค่าก่อนส่ง

    // $.ajax ({
    //   url: MealStoreUrl,
    //   type: 'POST',
    //   data: JSON.stringify (formData),
    //   headers: {
    //     'X-CSRF-TOKEN': $ ('meta[name="csrf-token"]').attr ('content'),
    //   },
    //   success: function (response) {
    //     if (response) {
    //         // console.log(response);
    //         Swal.fire({
    //             title: response.message,
    //             icon: response.class,
    //             customClass: {
    //                 confirmButton: 'btn btn-primary waves-effect waves-light'
    //             },
    //             buttonsStyling: false
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 window.location.href ="/Pricepermeal";
    //               }
    //         });
    //     }
    //   },
    //   error: function (xhr) {
    //     var errors = xhr.responseJSON.errors;

    //     // Display validation errors
    //     if (errors) {
    //       $.each (errors, function (field, message) {
    //          // แสดง Error สำหรับ plants
    //         if (field === "plants") {
    //             $("#plants-error").html(message[0]);
    //         }
    //         $ ('#' + field + '-error').html (message[0]);
    //       });
    //     }
    //   },
    // });
  });
//   End Save Main





});
