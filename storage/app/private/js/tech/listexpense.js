$(document).ready(function() {
    $('#ExpenseList').DataTable({
        processing: true,
        order: [
            [2, 'desc']
        ],
        lengthMenu: [5, 10, 25, 50, 75, 100],
    });

    $ ('#exdate').flatpickr ({
        monthSelectorType: 'static',
    });

    $ ('#end_exdate').flatpickr ({
        monthSelectorType: 'static',
    });
});