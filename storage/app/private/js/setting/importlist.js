$ (document).ready (function () {
    $ ('#importtable').DataTable ({
        processing: true,
        order: [[0, 'desc']],
        lengthMenu: [5, 10, 25, 50, 75, 100],
    });
});

$(document).on('click', '.deletegropspecial', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: 'คุณต้องการลบข้อมูลหรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            var url = ImportDelUrl.replace(':id', id);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _method: "DELETE", // Laravel requires this for AJAX PUT requests
                    _token: $ ('meta[name="csrf-token"]').attr ('content'),
                    id: id
                },
                success: function(response) {
                    if (response) {
                        Swal.fire({
                            title: response.message,
                            icon: response.class,
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        }).then (result => {
                            if (result.isConfirmed) {
                                location.reload ();
                            }
                          });
                    }
                }
            });
        }
    })
});
